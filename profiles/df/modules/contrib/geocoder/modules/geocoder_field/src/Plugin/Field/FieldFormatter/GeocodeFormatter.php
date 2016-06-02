<?php

/**
 * @file
 * Contains \Drupal\geocoder_field\Plugin\Field\FieldFormatter\GeocodeFormatter.
 */

namespace Drupal\geocoder_field\Plugin\Field\FieldFormatter;

use Drupal\geocoder_field\Plugin\Field\GeocodeFormatterBase;

/**
 * Plugin implementation of the Geocode formatter.
 *
 * @FieldFormatter(
 *   id = "geocoder_geocode_formatter",
 *   label = @Translation("Geocode"),
 *   field_types = {
 *     "string",
 *   }
 * )
 */
class GeocodeFormatter extends GeocodeFormatterBase {

}
