<?php

/**
 * @file
 * Contains \Drupal\geocoder_geofield\Plugin\Field\FieldWidget\GeofieldFromWidget.
 */

namespace Drupal\geocoder_geofield\Plugin\Field\FieldWidget;

use Drupal\geocoder_field\Plugin\Field\GeocodeFromWidgetBase;

/**
 * Geocode 'from' widget implementation for the Geocoder Geofield module.
 *
 * @FieldWidget(
 *   id = "geocoder_geofield_geocode_from_widget",
 *   label = @Translation("Geocode from an existing field"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
class GeofieldFromWidget extends GeocodeFromWidgetBase {

}
