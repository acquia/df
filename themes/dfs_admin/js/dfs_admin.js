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

        $('.color-preview').dialog({
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
