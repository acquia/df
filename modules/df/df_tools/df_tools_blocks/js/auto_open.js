/**
 * @file auto_open.js
 */
(function ($, Drupal) {

  "use strict";

  /**
   * Automatically opens certain block forms.
   */
  Drupal.behaviors.DFToolsBlocksAutoOpen = {
    attach: function (context) {
      $('[data-df-tools-blocks-auto-open]', context).once('auto-open').each(function () {
        var name = $(this).data('df-tools-blocks-auto-open');
        $(this).find('[name="' + name + '"]').click();
      });
    }
  };

}(jQuery, Drupal));
