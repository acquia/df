/**
 * @file magellan.js
 */
(function ($, Drupal) {

  "use strict";

  /**
   * Dynamically fills the "Magellan" block with valid links.
   */
  Drupal.behaviors.DFToolsBlocksMagellan = {
    attach: function (context) {
      $('.df-tools-magellan-block', context).once('magellan').each(function () {
        if (Drupal.panels_ipe && Drupal.panels_ipe.app && Drupal.panels_ipe.app.get('active')) {
          $(this).find('[data-sticky]').attr('style', '');
          $(this).find('[data-sticky]').removeClass('sticky');
          return;
        }
        var $blocks_after = $(this).parents().eq(1).nextAll();
        var $menu = $(this).find('.menu');
        $menu.empty();
        $blocks_after.each(function () {
          $(this).find('.block-title').each(function () {
            if (!this.hasAttribute('id')) {
              $(this).uniqueId();
            }
            $(this).attr('data-magellan-target', $(this).attr('id'));
            var $anchor = $('<a></a>');
            $anchor.attr('href', '#' + $(this).attr('id'));
            $anchor.text($(this).text());
            $menu.append($('<li></li>').append($anchor));
          });
        });
        $(this).find('[data-sticky], [data-magellan]').each(function() {
          if ($(this).data('zfPlugin')) {
            $(this).foundation('destroy');
          }
        });
        $(this).foundation();
        $(this).find('a').on('click', function () {
          $('[data-magellan]').data('zfPlugin').options.barOffset = $('#toolbar-bar:visible').outerHeight() + $('#toolbar-item-administration-tray:visible').outerHeight() + $(this).outerHeight() - 10;
        });
      });

      // Temporary fix until https://github.com/zurb/foundation-sites/issues/8016 is closed.
      $(window).once('temporary-magellan-fix').on('scroll hashchange', function () {
        $('[data-magellan]').each(function () {
          if ($(this).data('zfPlugin')) {
            $(this).foundation('reflow');
          }
        });
      });
    }
  };

}(jQuery, Drupal));
