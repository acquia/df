/**
 * @file df_tools_gallery.view.js
 */
(function ($, Drupal) {

  "use strict";

  /**
   * Registers behaviours related to view widget.
   */

  Drupal.behaviors.DFToolsGalleryView = {
    attach: function (context) {
      var container = $('.df-masonry-gallery');
      container.imagesLoaded(function() {
        container.masonry({
          columnWidth: '.df-masonry-gallery .grid-item',
          itemSelector: '.df-masonry-gallery .grid-item',
          percentPosition: true,
          gutter: 20
        });
      });
    }
  };

}(jQuery, Drupal));
