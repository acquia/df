/**
 * @file
 * Add search box focus
 */

(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.acmsSearch = {
    attach: function (context, settings) {
      $('.search-toggle-button').click(function(){
        setTimeout(function() { $('.search-wrapper input.form-text').focus()}, 20);
      });
    }
  }

})(jQuery, Drupal, drupalSettings);
