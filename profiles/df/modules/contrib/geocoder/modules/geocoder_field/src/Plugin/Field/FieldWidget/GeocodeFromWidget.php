<?php

/**
 * @file
 * Contains \Drupal\geocoder_field\Plugin\Field\FieldWidget\GeocodeFromWidget.
 */

namespace Drupal\geocoder_field\Plugin\Field\FieldWidget;

use Drupal\geocoder_field\Plugin\Field\GeocodeFromWidgetBase;

/**
 * Geocode widget implementation for the Geocoder Field module.
 *
 * @FieldWidget(
 *   id = "geocoder_geocode_from_widget",
 *   label = @Translation("Geocode from an existing field"),
 *   field_types = {
 *     "string",
 *     "file",
 *     "image"
 *   }
 * )
 */
class GeocodeFromWidget extends GeocodeFromWidgetBase {

}
