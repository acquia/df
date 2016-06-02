(function ($) {
  'use strict';
  Drupal.behaviors.addtoany = {
    attach: function (context, settings) {


      // Initial page setup

      var addtoany_icon = $('input[name="addtoany_universal_button"]').next('label').find('img:first');
      var initial_icon_size_int = parseInt($('input[name="addtoany_buttons_size"]').val());

      // Set the A2A icon's size to match selected Icon Size
      addtoany_icon.height(initial_icon_size_int).width(initial_icon_size_int);


      // Bring attention to large A2A icon option
      // because the universal button will likely be changed to match the other icons
      // (Drupal #states can't handle this)
      $('input[name="addtoany_buttons_size"]').change(function () {

        var icon_size = $(this).val();
        var icon_size_int = parseInt(icon_size);

        // Set the A2A icon's size to match selected Icon Size
        addtoany_icon.height(icon_size_int).width(icon_size_int);

      });


    }
  };

}(jQuery));
