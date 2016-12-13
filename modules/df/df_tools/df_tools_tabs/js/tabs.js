/**
 * @file tabs.js
 */
(function ($, Drupal) {

  "use strict";

  /**
   * Enables jQuery tabs for our field.
   */
  Drupal.behaviors.DFToolsTabs = {
    attach: function (context) {
      $('.df-tools-tabs-wrapper', context).once('tabs').tabs();
    }
  };

}(jQuery, Drupal));
