(function ($) {
  "use strict";

  // Cached reference to $(window).
  var $window = $(window);

  // The threshold for how far to the bottom you should reach before reloading.
  var scroll_threshold = 200;

  // The selector for the automatic pager.
  var automatic_pager_selector = '.infinite-scroll-automatic-pager';

  // The selector for the automatic pager.
  var content_wrapper_selector = '.views-infinite-scroll-content-wrapper';

  // The event and namespace that is bound to window for automatic scrolling.
  var scroll_event = 'scroll.views_infinite_scroll';

  /**
   * Insert a views infinite scroll view into the document.
   */
  $.fn.infiniteScrollInsertView = function ($new_view) {
    var $existing_view = this;
    var $existing_content = $existing_view.find(content_wrapper_selector).children();
    $existing_view.css('height', $existing_view.height() + 'px');
    $new_view.find(content_wrapper_selector).prepend($existing_content);
    $existing_view.replaceWith($new_view);
    $(document).trigger('infiniteScrollComplete', [$new_view, $existing_content]);
  };

  /**
   * Handle the automatic paging based on the scroll amount.
   */
  Drupal.behaviors.views_infinite_scroll_automatic = {
    attach : function(context, settings) {
      $(automatic_pager_selector, context).once().each(function() {
        var $pager = $(this);
        $pager.addClass('visually-hidden');
        $window.on(scroll_event, function() {
          if (window.innerHeight + window.pageYOffset > $pager.offset().top - scroll_threshold) {
            $pager.find('[rel=next]').click();
            $window.off(scroll_event);
          }
        });
      });
    },
    detach: function (context, settings, trigger) {
      // In the case where the view is removed from the document, remove it's
      // events. This is important in the case a view being refreshed for a reason
      // other than a scroll. AJAX filters are a good example of the event needing
      // to be destroyed earlier than above.
      if ($(automatic_pager_selector, context).length != 0) {
        $window.off(scroll_event);
      }
    }
  };

})(jQuery);
