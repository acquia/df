/**
 * @file
 * Generic functions for all our Foundation Scenarios
 *
 */
(function($, Drupal) {

  // Add the active class to the first available Foundation tab.
  //set active class for tabs for whatever is returned by views
  $(document).ready(function() {
    function openTab() {
      var FoundationTabA = $('.tabs li a');
      var FoundationFirstTabA = $('.tabs').find('li a').first();
      var FoundationFirstTabPanel = $('.tabs-panel').first();
      var origHash = window.location.hash;
      if (origHash) {
        var TabURL = '#' + window.location.hash.substr(1);
      };
      if (typeof TabURL !== 'undefined') {
        FoundationTabA.each(function(e) {
          var hash = $(this).attr('href');
          if (hash == TabURL) {
            $(this).click();
            $('html, body').animate({
              scrollTop: $('.tabs-title a').offset().top
            }, 2000);
            return false;
          }
        });
      } else {
        // open the first tab by default
        FoundationFirstTabA.click();
        FoundationFirstTabPanel.addClass('is-active');
      }
    }
    $(window).bind('hashchange', function() {
      openTab();
    });

    openTab();
  });

  //after 5 seconds, hide the notifcations
  function removeWhiteSpace() {
    $('.region-highlighted').removeClass('callout');
  }
  setTimeout(function() {
    elem = $(".zurb-foundation-callout")
    Foundation.Motion.animateOut(elem, 'hinge-out-from-top', removeWhiteSpace);
  }, 5000);

  //If workbench moderation form is on, move it to mm-panel. 
  //This is required as mmmenu cannot accept form elements, so we do it after dom load.
  $(document).ready(function() {
    $('.workbench-moderation-entity-moderation-form').appendTo('.mm-panel');
  });

  //when a comment link is clicked, smooth scroll
  var $root = $('html, body');
  $('.author-text-area a').click(function() {
    $root.animate({
      scrollTop: $($.attr(this, 'href')).offset().top - 150
    }, 1000);
    return false;
  });

  Drupal.behaviors.fullWidthEntityBrowserModal = {
    attach: function(context, settings) {
      var $child = $(context).children('iframe');
      if ($child.length > 0) {
        if ($child.attr('src').indexOf('/entity-browser/modal/browse_content_grid') == 0 ||
          $child.attr('src').indexOf('/entity-browser/modal/browse_files_modal') == 0) {
          var $modal = $('#drupal-modal');
          $modal.on('dialogopen', function(event, ui) {
            // Make the modal full width.
            $modal.dialog({
              width: '100%',
              height: $(window).height(),
              buttons: [{
                text: Drupal.t('Select'),
                click: function(e) {
                  $child.contents().find('#edit-actions-wrap input').click();
                  e.preventDefault();
                  e.stopPropagation();
                }
              }]
            });
            $modal.parent().addClass('ui-dialog-full-width');
            $child.css('height', $modal.innerHeight());
          });

          $child.on('load', function() {
            $child.contents().find('#edit-actions-wrap').hide();
          });
        }
      }
    }
  };

   Drupal.behaviors.IPETemplateChanges = {
    attach: function(context) {
      if (typeof Backbone !== 'undefined') {
        Backbone.on('PanelsIPEInitialized', function() {
          Drupal.panels_ipe.app_view.tabsView.tabViews['manage_content'].on('render', function() {
            var html = $('[data-category="Create Content"]').html();
            html = html.replace('Create Content', 'Create Display');
            $('[data-category="Create Content"]').html(html);
          }, 'fin');

          Drupal.panels_ipe.app_view.tabsView.tabViews['manage_content'].template_form = _.template(
            '<% if (typeof(plugin_id) !== "undefined") { %>' +
            '<h4>' + Drupal.t('Configure <strong><%- label %></strong> block') + '</h4>' +
            '<% } else { %>' +
            '<h4>' + Drupal.t('Create new <strong><%- label %></strong> display') + '</h4>' +
            '<% } %>' +
            '<div class="ipe-block-form ipe-form"><div class="ipe-icon ipe-icon-loading"></div></div>'
          );
        });
      }
    }
  };

})(jQuery, Drupal);
