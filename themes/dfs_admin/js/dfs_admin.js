/**
 * @file
 * Javascript behaviors which enhance the Material Admin theme.
 *
 */

(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.DFSAdmin = {
    attach: function (context, settings) {

      $(function dfsModal () {
         // Turn color preview h2 into a button
        $('.color-form').find('h2').replaceWith(function() {
          return '<a class="btn color-preview-preview">' + $(this).text() + '</a>';
        });

        var mWidth = $(window).width()-20;
        var mHeight = $(window).height()-20;

        // Because we hide the body scrollbar our modal is not centered
        var scrollbarWidth=(window.innerWidth-$(window).width());
        var cModal = Math.floor(10 + (scrollbarWidth/2));

        $('.color-preview').dialog({
          open: function () {
            $('body').css('overflow','hidden');
            // Update left to make modal centered
            $('.color-preview').parent().css({'left': cModal});
          },
          close: function () {
            $('body').css('overflow','scroll');
          },
          autoOpen: false,
          title: 'Color Preview',
          width: mWidth,
          maxHeight: mHeight,
          modal: true,
          draggable: false,
          resizable: false,
        });

        $('.color-preview-preview').on('click', function() {
          $('.color-preview').dialog('open');
        });
      });
    }
  }

})(jQuery, Drupal, drupalSettings);
