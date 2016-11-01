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
            close: function() {
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

})(jQuery, Drupal);
