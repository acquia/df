/**
 * @file
 * Generic functions for all our Foundation Scenarios
 *
 */

(function ($, Drupal) {

  // FOUC fix for menu.
  function showMenu () {
    $('#top-bar-sticky-container').show();
  }

  $(document).ready(function () {
    if (Foundation.MediaQuery.atLeast('medium')) {
      showMenu();
    }

    $(window).on('changed.zf.mediaquery', function (event, medium, small) {
      showMenu();
    });
  });

  // Add the active class to the first available Foundation tab.
  $(document).ready(function () {
    function openTab () {
      var FoundationTabA = $('.tabs li a');
      var FoundationFirstTabA = $('.tabs').find('li a').first();
      var FoundationFirstTabPanel = $('.tabs-panel').first();
      var origHash = window.location.hash;
      if (origHash) {
        var TabURL = '#' + window.location.hash.substr(1);
      }
      if (typeof TabURL !== 'undefined') {
        FoundationTabA.each(function (e) {
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
        // Open the first tab by default.
        FoundationFirstTabA.click();
        FoundationFirstTabPanel.addClass('is-active');
      }
    }

    $(window).bind('hashchange', function () {
      openTab();
    });

    openTab();
  });

  // After 5 seconds, hide notifications.
  function removeWhiteSpace () {
    $('.region-highlighted').removeClass('callout');
  }

  setTimeout(function () {
    elem = $('.zurb-foundation-callout');
    Foundation.Motion.animateOut(elem, 'hinge-out-from-top', removeWhiteSpace);
  }, 5000);

  // When a comment link is clicked, smooth scroll.
  var $root = $('html, body');
  $('.author-text-area a').click(function () {
    $root.animate({
      scrollTop: $($.attr(this, 'href')).offset().top - 150
    }, 1000);
    return false;
  });

  $(window).on({
    'dialog:aftercreate': function (event, dialog, $modal, settings) {
      var $child = $modal.find('iframe');
      if ($child.length > 0) {
        if ($child.attr('src').indexOf('/entity-browser/modal/browse_content') == 0 ||
          $child.attr('src').indexOf('/entity-browser/iframe/media_browser') == 0) {
          // Make the modal full width.
          $modal.dialog({
            width: '100%',
            height: $(window).height(),
            close: function () {
              $('body').removeClass('full-width-modal-open');
            }
          });
          $modal.parent().addClass('ui-dialog-full-width');
          $child.css('height', $modal.innerHeight());
          $('body').addClass('full-width-modal-open');
        }
      }
    }
  });

  Drupal.behaviors.DFSBaseformFilled = {
    attach: function (context, settings) {
      var formID = $('.contact-form');
      var checkbox = $('.form-checkbox');
      var radio = $('.form-radio');
      var textArea = $('.form-textarea');
      var textItem = $('.form-text');
      var select = $('.form-select');

      function isChecked () {
        $(this).each(function () {
          var $label = $(this).next('label');
          $(this).prop('checked') ? $label.addClass('item-selected') : $label.removeClass('item-selected');
        });
        var limit = 3;
        if ($(this).parent().siblings().children(':checked').length > limit) {
          $(this).closest('fieldset').find('legend').addClass('alert');
          $(this).closest('fieldset').find('.description').addClass('alert');
        } else {
          $(this).closest('fieldset').find('legend').removeClass('alert');
          $(this).closest('fieldset').find('.description').removeClass('alert');
        }
      }

      function isFilled () {
        $(this).each(function () {
          var $label = $(this).closest('.form-item').find('label');
          ($(this).val().trim().length > 0) ? $label.addClass('item-selected') : $label.removeClass('item-selected');
        })
      }

      $(document).change(function () {
        if (formID.length) {
          isChecked.call(checkbox);
          isChecked.call(radio);
          isFilled.call(textArea);
          isFilled.call(textItem);
          isFilled.call(select);
        }
      });

      $(document).ready(function () {
        if (formID.length) {
          isChecked.call(checkbox);
          isChecked.call(radio);
          isFilled.call(textArea);
          isFilled.call(textItem);
          isFilled.call(select);
        }
      });
    }
  };

  // Another foundation bug on resize. When the window resizes, the hidden
  // dropdown page causes the page to expand beyond window size.
  // It only uses visibility hidden, but we need display none.
  Drupal.behaviors.DFSBaseFoundationUser = {
    attach: function (context, settings) {
      var dropDownButton = $('.login-dropdown-button');
      var languageDownButton = $('.language-dropdown-button');
      var dropDownPane = $('.dropdown-pane');
      var languagePane = $('.language-pane');
      // Add class on page load.
      $(document).ready(function () {
        dropDownPane.addClass('display-none');
        languagePane.addClass('display-none');
      });
      // Toggle class on click.
      dropDownButton.on('click', function () {
        dropDownPane.removeClass('display-none');
      });
      languageDownButton.on('click', function () {
        languagePane.removeClass('display-none');
      });
    }
  };

  // Temp fix until this is resolved: http://foundation.zurb.com/forum/posts/38056-responsive-navigation-problem
  Drupal.behaviors.DFSBaseFoundationMenu = {
    attach: function (context, settings) {
      var Icon = $('button.menu-icon');
      var topbar = $('.top-bar');
      var topbarDrop = $('.topbar-toplevel');
      Icon.click(function (t) {
        topbarDrop.toggleClass('vertical');
      });

      $(window).on('changed.zf.mediaquery', function (event, medium, small) {
        if ((topbarDrop).hasClass('vertical')) {
          topbarDrop.toggleClass('vertical');
        }
      });
    }
  };

  // Remove sticky nav when Quick Edit is turned on as it causes all kinds of
  // complications.
  Drupal.behaviors.DFSBaseQuickEdit = {
    attach: function (context, settings) {
      $(document).on('DOMNodeInserted', function (e) {
        if (e.target.id == 'quickedit-entity-toolbar') {
          $('body').addClass('quickedit-on');
          $('.top-bar-wrapper .sticky').removeAttr('data-sticky').removeClass('sticky');
        }
      });
      $(document).on('DOMNodeRemoved', function (e) {
        if (e.target.id == 'quickedit-entity-toolbar') {
          $('body').removeClass('quickedit-on');
          $('.top-bar-wrapper .sticky-container > div').addClass('sticky').attr('data-sticky');
        }
      });
    }
  };

  // Sometimes we get weird issues with images not loading due to image styles,
  // so at least provide a fallback.
  Drupal.behaviors.DFSBaseimgError = {
    attach: function (context, settings) {
      $('img').one('error', function () {
        var image = this;
        var thisSrc = this.src;
        var matchSitesDefault = thisSrc.match(/\/sites\/(.*)\/files\/styles\//);
        var width = $(image).attr('width');
        var height = $(image).attr('height');

        if (matchSitesDefault) {
          console.log('ATTENTION: The image on this page may be broken because a Dev Desktop bug, please go to /admin/appearance/ and save the form');
        }
        // If both elements are undefined, just hide them.
        if (!height && !width) {
          $(image).hide();
          console.log('404 broken image hidden with this path:' + thisSrc);
        } else {
          // Take attr and move it to css height/width. then display the.
          $(image).wrap("<span class='broken-image-wrap'></span>");
          $(image).parent().css({
            'height': height,
            'width': width,
          });
          // Still hide the image to remove browser default icon, because we
          // are now using background image.
          $(image).hide();
        }
      });
    }
  };

})(jQuery, Drupal);
