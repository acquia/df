<?php

/**
 * @file
 * Contains \Drupal\geocoder_field\Plugin\Field\FieldWidget\GeocodeFromWidgetBase.
 */

namespace Drupal\geocoder_field\Plugin\Field;


/**
 * Base geocode 'from' widget implementation for the Geocoder Field module.
 */
abstract class GeocodeFromWidgetBase extends GeocodeWidgetBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'geocoder_type' => 'from',
    ) + parent::defaultSettings();
  }

}
