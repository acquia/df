/**
 * @file
 * Overrides the Content Browser module
 *
 */

(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.DFSAdminContentBrowser = {
    attach: function (context, settings) {
      $('.views-row').once('bind-click-event').click(function  (e) {
        e.preventDefault();
        var input = $(this).find('.views-field-entity-browser-select input');
        input.prop('checked', !input.prop('checked'));
        if (input.prop('checked')) {
          $(this).addClass('checked');
        }
        else {
          $(this).removeClass('checked');
        }
      });
    }
  }

})(jQuery, Drupal, drupalSettings);
