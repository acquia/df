/**
 * @file
 * Adds a helper class to visually select entities.
 *
 * Should be functional for any view with a checkbox field.
 *
 */

(function ($, Drupal) {

  Drupal.behaviors.DFSAdminBrowsers = {
    attach: function (context, settings) {
      $('.views-row').once('bind-click-event').click(function  (e) {
        e.preventDefault();
        var input = $(this).find('input[type="checkbox"]');
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

})(jQuery, Drupal);
