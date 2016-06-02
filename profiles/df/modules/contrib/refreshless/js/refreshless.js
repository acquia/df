(function ($, Drupal, drupalSettings, history, JSON, storage) {

  /* eslint-disable no-console */

  'use strict';

  var relativeLinksSelector = 'a:not([data-refreshless-exclude])[href^="/"], a[href^="#"]';

  // Tracks the current position in the History API.
  // @todo Make this not be a global.
  var currentPos = null;

  /**
   * @typedef {object} Url
   *
   * @prop {string} absoluteUrl
   *   The absolute URL, including scheme, host, path, query string, fragment.
   * @prop {string} requestUrl
   *   The URL minus the fragment, if any. To use in a request to the server.
   * @prop {?string} [fragment]
   *   The fragment of the URL, if any. Without the '#' character.
   */

  /**
   * @constructor
   *
   * @param {string} url
   *   A URL: can be an absolute URL, a relative URL, a fragment URL: any URL.
   */
  function Url(url) {
    var link = document.createElement('a');
    link.href = url;
    var fragmentLength = link.hash.length;
    this.absoluteUrl = link.href;
    if (fragmentLength < 2) {
      this.requestUrl = this.absoluteUrl;
    }
    else {
      this.requestUrl = this.absoluteUrl.slice(0, -fragmentLength);
      this.fragment = link.hash.slice(1);
    }
  }

  /**
   * State object to track changes in History API.
   *
   * @typedef {object} State
   *
   * @prop {number} pos
   *   The position in the history. Non-negative integer.
   * @prop {string} type
   *   One of `'inter'` (inter-page navigation: requires talking to server) or
   *   `'intra'` (intra-page navigation: requires no talking to server).
   * @prop {string} absoluteUrl
   *   {@link Drupal.Ajax#absoluteUrl}
   * @prop {string} requestUrl
   *   {@link Drupal.Ajax#requestUrl}
   * @prop {string|bool} fragment
   *   {@link Drupal.Ajax#fragment} if set, otherwise `false`.
   * @prop {?object} content
   *   If type `'inter'`, then an object with the data representing the content.
   *
   * For state management. To be stored in History API's "state" entries.
   *
   * Guaranteed to be smaller than 640K, to ensure a fit.
   * @see https://developer.mozilla.org/en-US/docs/Web/API/History_API
   *
   * For example, when this is the navigation history:
   * 1. /foo
   * 2. /bar
   * 3. /bar#llama
   * 4. /bar#alpaca
   * 5. /baz
   *
   * Then these are the stored state changes:
   * - 1 -> 2 = inter (stored in Drupal.refreshless.state.2)
   * - 2 -> 3 = intra (stored in Drupal.refreshless.state.3)
   * - 3 -> 4 = intra (stored in Drupal.refreshless.state.4)
   * - 4 -> 5 = inter (stored in Drupal.refreshless.state.5)
   *
   * And if navigating back can be somewhat tricky:
   * - Going back from 3 to 2, or 4 to 3 are both 'intra' changes: the content
   *   remains the same.
   * - Going back from 5 to 4 is an 'inter', but cannot use 5's `State`, since
   *   that itself was merely an 'intra'. We need to go back through all
   *   consecutive 'intra's until we encounter an inter (2), and apply those
   *   HTML-changing AJAX commands, and *then* perform the 'intra' navigation.
   * - In other words: each history state entry records data and its type in
   *   relation to the previous state in the sequence. When going backwards,
   *   that requires the extra care just explained.
   */
  var State = (function () {

    /**
     * @constructor
     *
     * @param {number} historyPosition
     *   A non-negative number.
     * @param {string} type
     *   One of `'inter'` (inter-page navigation: requires talking to server) or
     *   `'intra'` (intra-page navigation: requires no talking to server).
     * @param {string} navigationUrl
     *   The URL to navigate to.
     */
    function State(historyPosition, type, navigationUrl) {
      if (arguments.length === 1) {
        var object = arguments[0];
        Object.keys(object).forEach(function (key) {
          this[key] = object[key];
        }, this);
        return;
      }
      this.pos = historyPosition;
      this.type = type;
      var url = new Url(navigationUrl);
      this.absoluteUrl = url.absoluteUrl;
      this.requestUrl = url.requestUrl;
      this.fragment = url.fragment ? url.fragment : false;
      // For type = inter only.
      this.content = null;
    }
    State.prototype.getId = function () {
      return State.getIdForPos(this.pos);
    };
    State.prototype.getType = function () {
      return this.type;
    };
    State.prototype.store = function (method) {
      storage.setItem(this.getId(), JSON.stringify(this));
      history[method + 'State'](this.getId(), null, this.absoluteUrl);
    };

    /**
     * Finds the `type=inter` state to restore when navigating back.
     *
     * @return {State}
     *   The `State` of `type='inter'` to restore.
     */
    State.prototype.findInterState = function () {
      var state = this;
      while (state.type !== 'inter') {
        state = State.fromPos(state.pos - 1);
      }
      return state;
    };
    State.getIdForPos = function (pos) {
      return 'Drupal.refreshless.state.' + pos;
    };
    State.isValidId = function (id) {
      return typeof id === 'string' && id.substr(0, 24) === 'Drupal.refreshless.state';
    };
    State.fromId = function (id) {
      return new State(JSON.parse(storage.getItem(id)));
    };
    State.fromPos = function (pos) {
      return State.fromId(State.getIdForPos(pos));
    };

    return State;
  })();

  /**
   * History-based navigation: back/forward buttons, so both directions.
   *
   * Scroll handling is performed by History API: scrollRestoration = auto.
   * @see https://html.spec.whatwg.org/multipage/browsers.html#dom-history-scroll-restoration
   *
   * @fixme Uses the currentPos global.
   */
  var HistoryNavigation = (function () {

    function HistoryNavigation() {
      this.handlePopState = this.handlePopState.bind(this);
    }

    HistoryNavigation.prototype.handlePopState = function (event) {
      // Only react to PopState events that contain data that we know how to deal
      // with. There can be other JavaScript (e.g. progressively decoupled
      // components) on the page that also use the History API. This allows those
      // to gracefully coexist.
      if (!State.isValidId(event.state)) {
        return;
      }

      var fromState = State.fromPos(currentPos);
      var toState = State.fromId(event.state);
      var direction = fromState.pos < toState.pos ? 'forward' : 'backward';
      var type = direction === 'forward' ? toState.type : fromState.type;
      currentPos = toState.pos;

      console.debug('Navigated', direction, 'from', fromState.pos, 'to', toState.pos, 'type', type, '(' + fromState.absoluteUrl + ' → ' + toState.absoluteUrl + ')');

      // We rely on History API's scrollRestoration=auto, so just ensure the right
      // HTML is present: only deal with transition type=inter.
      if (type === 'inter') {
        var state = (direction === 'forward') ? toState : toState.findInterState();

        // Simulate a Refreshless response having arrived, and let the Ajax system
        // handle it.
        var ajaxObject = createAjaxObject('');
        var fakeResponse = (direction === 'forward') ? state.content.htmlChangingCommands : buildBackwardsResponse(fromState, toState);
        var settingsWithAdditiveLibraries = $.extend({}, state.content.drupalSettings);
        settingsWithAdditiveLibraries.ajaxPageState.libraries = drupalSettings.ajaxPageState.libraries;
        fakeResponse.unshift({
          command: 'settings',
          settings: settingsWithAdditiveLibraries,
          merge: true
        });
        ajaxObject.success(fakeResponse, 'success');
        debugInterState(false);
      }
    };

    /**
     * Finds the previous HTML for a region.
     *
     * Starts in the given state, keeps navigating backwards through history until
     * it is encountered (i.e. the last time it was changed). Worst case, this
     * iterates all the way back to the root state. The root state contains all
     * regions' HTML and therefore the backtracking is guaranteed to end.
     *
     * @param {State} state
     *   The state we're navigating back in history to.
     * @param {string} regionName
     *   The name of the region.
     * @return {string}
     *   The HTML for the region.
     */
    function findPreviousHtmlForRegion(state, regionName) {
      var previousInterState = state.findInterState();
      var regions = previousInterState.content.htmlChangingCommands.reduce(function (memo, command) {
        if (command.command === 'refreshlessUpdateRegion') {
          memo[command.region] = command.data;
        }
        return memo;
      }, {});
      if (regions.hasOwnProperty(regionName)) {
        return regions[regionName];
      }
      else {
        return findPreviousHtmlForRegion(State.fromPos(previousInterState.pos - 1), regionName);
      }
    }

    function buildBackwardsResponse(fromState, toState) {
      var response = [];
      var currentHtmlCommands = fromState.content.htmlChangingCommands;
      for (var i = 0; i < currentHtmlCommands.length; i++) {
        var command = currentHtmlCommands[i];
        if (command.command === 'refreshlessUpdateRegion') {
          var regionName = command.region;
          response.push({
            command: 'refreshlessUpdateRegion',
            region: regionName,
            data: findPreviousHtmlForRegion(toState, regionName)
          });
        }
        else {
          response.push(
            toState.findInterState().content.htmlChangingCommands.filter(function (command) { return command.command === 'refreshlessUpdateHtmlHead'; })[0]
          );
        }
      }

      return response;
    }

    return HistoryNavigation;

  })();

  /**
   * Link-based navigation, which only goes forward relative to the current pos.
   *
   * Explicit scroll handling.
   */
  var LinkNavigation = (function () {

    function LinkNavigation() {
      this.handleClick = this.handleClick.bind();
    }

    LinkNavigation.prototype.handleClick = function (event) {
      // Middle click, cmd click, and ctrl click should open links as normal.
      if (event.which > 1 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
      }

      var target = new Url(event.currentTarget.href);
      if (urlUsesDifferentTheme(target.requestUrl)) {
        return;
      }

      if (!event.isDefaultPrevented()) {
        event.preventDefault();
        Drupal.RefreshLess.visit(target);
      }
    };

    function urlUsesDifferentTheme(url) {
      // Links pointing to a route that uses a different theme need full page
      // reloads. Any route can use any arbitrary theme based on arbitrary rules.
      // But, the 99% case is:
      // - a site with two themes: a front-end and an admin theme
      // - all '/admin/*' routes use the admin theme
      // We can optimize for that common case, and handle the uncommon case by
      // attempting to do a Refreshless-accelerated page load, and if the theme
      // doesn't match the current theme, do a full reload anyway.
      // @see \Drupal\system\EventSubscriber\AdminRouteSubscriber
      // @see …
      var urlIsAdmin = url.startsWith(drupalSettings.path.baseUrl + drupalSettings.path.pathPrefix + 'admin');
      return drupalSettings.path.currentPathIsAdmin !== urlIsAdmin;
    }

    return LinkNavigation;

  })();

  /**
   * Sets up root state and event listeners, provides API to visit URLs.
   *
   * Handles link-based navigation. Keeps track of state using State.
   *
   * @fixme Uses the currentPos global.
   */
  var Controller = (function () {

    function Controller() {
      this.historyNavigation = new HistoryNavigation();
      this.linkNavigation = new LinkNavigation();
    }

    Controller.prototype.start = function () {
      this.recordRootState();
      this.addEventListeners();
    };

    Controller.prototype.recordRootState = function () {
      currentPos = history.length - 1;

      var root = storage.getItem('Drupal.refreshless.root');
      if (root === null) {
        root = currentPos;
        storage.setItem('Drupal.refreshless.root', root);
      }
      else {
        root = parseInt(root);
      }

      if (root === currentPos) {
        var rootState = new State(root, 'inter', window.location);
        rootState.content = buildPageStateContent([], buildRootResponse(), drupalSettings);
        rootState.root = true;
        rootState.store('replace');
      }
    };

    Controller.prototype.addEventListeners = function () {
      window.addEventListener('popstate', this.historyNavigation.handlePopState);
      // @todo Convert to not use jQuery.
      jQuery('body').once('refreshless').on('click', relativeLinksSelector, this.linkNavigation.handleClick);

      window.addEventListener('refreshless:load', this.announceNewPage);
    };

    Controller.prototype.announceNewPage = function () {
      // Announce new page load for screenreader and other assistive technology.
      Drupal.announce(Drupal.t('New page loaded: !title', {'!title': document.title}), 'assertive');
    };

    Controller.prototype.visit = function (navigationUrl) {
      var target = (navigationUrl instanceof Url) ? navigationUrl : new Url(navigationUrl);
      var current = new Url(location);
      if (current.requestUrl === target.requestUrl) {
        followIntra(target);
      }
      else {
        followInter(target);
      }
    };

    /**
     * Helpers
     */

    function followIntra(target) {
      var state = new State(history.length, 'intra', target.absoluteUrl);
      state.store('push');
      currentPos++;
      scrollToTop(target.fragment);
    }

    function followInter(target) {
      var ajaxObject = createAjaxObject(target.requestUrl);

      // When the Refreshless request receives a succesful response, update the
      // URL using the history.pushState() API.
      var originalSuccess = ajaxObject.success;
      ajaxObject.success = function (response, status, xmlhttprequest) {
        var librariesBefore = getLibraries();

        originalSuccess.call(this, response, status, xmlhttprequest);

        var state = new State(history.length, 'inter', getCurrentAbsoluteUrl(target.fragment));
        state.content = buildPageStateContent(librariesBefore, response, drupalSettings);
        state.store('push');
        currentPos++;
        debugInterState(true);

        scrollToTop(target.fragment);
      };

      ajaxObject.execute();
    }

    // It is necessary to calculate the absolute URL, because the AJAX request's
    // URL may have redirected elsewhere.
    // @see refreshless_js_settings_alter()
    function getCurrentAbsoluteUrl(fragment) {
      return drupalSettings.path.refreshless_absolute_url + (fragment ? '#' + fragment : '');
    }

    function buildRootResponse() {
      var rootResponse = [];
      var regions = document.querySelectorAll('[data-refreshless-region]');
      for (var i = 0; i < regions.length; i++) {
        var regionName = regions[i].getAttribute('data-refreshless-region');
        rootResponse.push({
          command: 'refreshlessUpdateRegion',
          region: regionName,
          data: document.querySelector('[data-refreshless-region=' + regionName + ']').outerHTML
        });
      }

      var $headMarkup = getUpdatableHead();
      var headMarkup = '';
      for (var j = 0; j < $headMarkup.length; j++) {
        headMarkup += $headMarkup[j].outerHTML;
      }
      rootResponse.push({
        command: 'refreshlessUpdateHtmlHead',
        title: document.title,
        headMarkup: headMarkup
      });
      return rootResponse;
    }

    /**
     * Scrolls to the top of the document or the specified fragment.
     *
     * @param {?string} fragment
     *   When specified, the fragment whose top to scroll to. If not specified,
     *   the document's top will be scrolled to.
     */
    function scrollToTop(fragment) {
      if (fragment) {
        var element = document.getElementById(fragment);
        if (element) {
          element.scrollIntoView();
        }
      }
      else {
        window.scrollTo(0, 0);
      }
    }

    // librariesBefore: before response is applied
    // response: the response to apply
    // drupalSettings: after the response is applied
    function buildPageStateContent(librariesBefore, response, drupalSettings) {
      var updateHtmlHeadCommand = response.filter(function (command) { return command.command === 'refreshlessUpdateHtmlHead'; })[0];

      return {
        htmlChangingCommands: response.filter(function (command) { return command.command === 'refreshlessUpdateHtmlHead' || command.command === 'refreshlessUpdateRegion'; }),
        drupalSettings: jQuery.extend({}, drupalSettings),
        loadedLibraries: getLibraries().filter(function (value) { return !librariesBefore.includes(value); }),
        title: updateHtmlHeadCommand.title
      };
    }

    return Controller;

  })();

  function createAjaxObject(url) {
    // Create a Drupal.Ajax object without associating an element, a
    // progress indicator or a URL.
    var ajaxObject = Drupal.ajax({
      url: url,
      base: false,
      element: false,
      // @todo refreshless progress?
      progress: false,
      dialogType: 'refreshless'
    });
    var ajaxInstanceIndex = Drupal.ajax.instances.length;

    // Use GET, not the default of POST.
    ajaxObject.options.type = 'GET';

    // @see refreshless_js_settings_alter()
    var originalSettingsCommand = ajaxObject.commands.settings;
    ajaxObject.commands.settings = function (ajax, response, status) {
      drupalSettings.path = response.settings.refreshless.path;
      originalSettingsCommand(ajax, response, status);
    };

    // The server responds with a 412 when the current page's theme doesn't
    // match the response's theme. This means we need to do a full reload
    // after all.
    ajaxObject.options.error = function (response, status, xmlhttprequest) {
      if (response.status === 412) {
        window.location = url;
      }
    };

    // When the Refreshless request receives a succesful response, update the
    // URL using the history.pushState() API.
    var originalSuccess = ajaxObject.success;
    ajaxObject.success = function (response, status, xmlhttprequest) {
      originalSuccess.call(this, response, status, xmlhttprequest);

      var event = new Event('refreshless:load');
      window.dispatchEvent(event);

      // Set this to null and allow garbage collection to reclaim
      // the memory.
      Drupal.ajax.instances[ajaxInstanceIndex] = null;
    };

    // Pass Refreshless' page state, to allow the server to determine which
    // parts of the page need to be updated and which don't.
    ajaxObject.options.data.refreshless_page_state = drupalSettings.refreshlessPageState;

    return ajaxObject;
  }

  function getLibraries() {
    return drupalSettings.ajaxPageState.libraries.split(',');
  }

  function debugInterState(isNewHistory) {
    var url = drupalSettings.path.currentPath;
    var state = State.fromId(history.state).findInterState();
    var replacedRegions = state.content.htmlChangingCommands.filter(function (command) { return command.command === 'refreshlessUpdateRegion'; });
    if (isNewHistory) {
      console.debug('Updated HTML using the received Refreshless response for "' + url + '".');
    }
    else {
      console.debug('Updated HTML without performing a request: used state stored in History API.');
    }
    console.debug('  Replaced ' + replacedRegions.length + ' out of ' + document.querySelectorAll('[data-refreshless-region]').length + ' regions (' + replacedRegions.map(function (command) { return command.region; }).join(', ') + ').');
    if (isNewHistory) {
      console.debug('  Loaded ' + state.content.loadedLibraries.length + ' additional asset libraries: ', state.content.loadedLibraries);
    }
  }

  /**
   * Command to insert new content into the DOM.
   *
   * @param {Drupal.Ajax} ajax
   *   {@link Drupal.Ajax} object created by {@link Drupal.ajax}.
   * @param {object} response
   *   The response from the Ajax request.
   * @param {string} response.region
   *   The region name.
   * @param {string} response.data
   *   The new markup for the given region.
   * @param {number} [status]
   *   The XMLHttpRequest status.
   */
  Drupal.AjaxCommands.prototype.refreshlessUpdateRegion = function (ajax, response, status) {
    // @see refreshless_preprocess_region()
    response.selector = '[data-refreshless-region=' + response.region + ']';

    this.insert(ajax, response, status);
  };

  function getUpdatableHead() {
    var $head = jQuery('head');
    var $start = $head.find('meta[name=refreshless-head-marker-start]');
    return $start.nextUntil('meta[name=refreshless-head-marker-stop]');
  }

  /**
   * Command to update HTML <head> when using Refreshless.
   *
   * @param {Drupal.Ajax} ajax
   *   {@link Drupal.Ajax} object created by {@link Drupal.ajax}.
   * @param {object} response
   *   The response from the Ajax request.
   * @param {string} response.title
   *   The new title for the page.
   * @param {string} response.headMarkup
   *   The new markup for the HTML <head>.
   * @param {number} [status]
   *   The XMLHttpRequest status.
   */
  Drupal.AjaxCommands.prototype.refreshlessUpdateHtmlHead = function (ajax, response, status) {
    document.title = response.title;
    getUpdatableHead()
      .remove().end()
      .after(response.headMarkup);
  };

  var controller = new Controller();
  controller.start();

  Drupal.RefreshLess = {
    visit: function (location) {
      return controller.visit(location);
    }
  };

})(jQuery, Drupal, drupalSettings, window.history, window.JSON, window.sessionStorage);
