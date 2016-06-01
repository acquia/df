/**
 * @file iconpicker.js
 */
(function ($, Drupal) {

  "use strict";

  /**
   * Attaches fontawesome-iconpicker to particular textfields.
   */
  Drupal.behaviors.DFToolsIconIconPicker = {
    attach: function (context) {
      $('.enable-fontawesome-iconpicker').once().each(function() {
        var $component = $('<span class="iconpicker-component"><i class="fa fa-archive"></i></span>');
        $(this).after($component);
        $(this).iconpicker({
          component: '.iconpicker-component',
          placement: 'bottomRight'
        });

        // Resize the iconpicker relative to the input.
        $component.css('height', $(this).outerHeight() - 2);
        $component.children('i').css('margin-top', ($component.outerHeight() - 16) / 2);
      });
    }
  };

}(jQuery, Drupal));
