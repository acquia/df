/**
 * @file
 * Contains Javascript required to format the form
 */

(function ($, Drupal) {

  "use strict";

  Drupal.behaviors.DFToolsSlideshow = {
    attach: function (context, settings) {
      for (var i in settings.DFToolsSlideshow) {
        var config = settings.DFToolsSlideshow[i].slick;
        var $slideshow = $('[data-df-tools-slideshow-instance-id="' + i + '"] > .field-items').not('.slick-initialized');
        if ($slideshow.length) {
          if (config.customPaging === true) {
            config.customPaging = function (slider, i) {
              var $slide = $(slider.$slides[i]);
              return $('<a href="javascript:;" class="df-tools-slideshow-custom-pager"></a>').append($slide.find('.df-tools-slideshow-pager-element').clone());
            };
            $slideshow.addClass('df-tools-slideshow-uses-custom-pager');
          }
          $slideshow.slick(config);
        }
      }
    }
  };

}(jQuery, Drupal));
