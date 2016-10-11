/**
 * @file df_tools_map.js
 */
(function ($, Drupal) {

  "use strict";

  /**
   * Adds HTML5 geolocation support to our Views exposed form.
   */
  Drupal.behaviors.DFToolsMapSimpleProximity = {
    attach: function (context) {
      if (!navigator.geolocation) {
        $('.simple-proximity-location-button').hide();
      }
      else {
        // Perform a permissions check and hide the button if permissions are
        // defined.
        if (navigator.permissions && navigator.permissions.query) {
          navigator.permissions.query({name: 'geolocation'}).then(function (permission) {
            if (permission.state == 'denied') {
              $('.simple-proximity-location-button').hide();
            }
          });
        }

        // Attach a normal click handler to prompt the user for their location.
        // In Chrome, this only works over HTTPS.
        $('.simple-proximity-location-button').on('click', function () {
          var $target = $('.simple-proximity-location');
          $target.val(Drupal.t('Searching...'));
          navigator.geolocation.getCurrentPosition(function (position) {
            $target.val(position.coords.latitude + ',' + position.coords.longitude);
            $target.closest('.view-filters').find('input[type="submit"]').click();
          });
        });
      }
    }
  };

}(jQuery, Drupal));
