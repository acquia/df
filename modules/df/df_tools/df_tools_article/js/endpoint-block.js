/**
 * @file
 * api modal
 */

(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.apiDemo = {
    attach: function (context, settings) {

      $(function apiDemoModal () {
        var mWidth = $(window).width()-20;
        var mHeight = $(window).height()-20;

        // Because we hide the body scrollbar our modal is not centered.
        var scrollbarWidth=(window.innerWidth-$(window).width());
        var cModal = Math.floor(10 + (scrollbarWidth/2));

        $('.apiResponseModal').dialog({
          open: function () {
            $('.ui-widget-overlay').bind('click', function () {
               $(".apiResponseModal").dialog('close');
             });
            $('body').css('overflow','hidden');
            // Update left to center modal.
            $('.apiResponseModal').parent().css({'left': cModal});
          },
          close: function () {
            $('body').css('overflow','scroll');
          },
          autoOpen: false,
          title: 'API Response',
          width: mWidth,
          maxHeight: mHeight,
          modal: true,
          draggable: true,
          resizable: false,
        });

        // Show horizontal scroll if vertical scroll is present.
        $('.ui-dialog-content pre').css('max-height', mHeight-100);

        $('.open-apiModal').on('click', function() {
          $('.apiResponseModal').dialog('open');
        });

      });
    }
  }

})(jQuery, Drupal, drupalSettings);
