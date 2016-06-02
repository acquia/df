<?php

/**
 * @file
 * Contains \Drupal\geocoder_geofield\Plugin\Field\FieldWidget\ReverseGeofieldFromWidget.
 */

namespace Drupal\geocoder_geofield\Plugin\Field\FieldWidget;

use Drupal\geocoder_field\Plugin\Field\FieldWidget\ReverseGeocodeFromWidget;

/**
 * Reverse geocode 'from' widget implementation for the Geocoder Geofield module.
 *
 * @FieldWidget(
 *   id = "geocoder_geofield_reverse_geocode_from_widget",
 *   label = @Translation("Reverse geocode from an existing field"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
class ReverseGeofieldFromWidget extends ReverseGeocodeFromWidget {

}
