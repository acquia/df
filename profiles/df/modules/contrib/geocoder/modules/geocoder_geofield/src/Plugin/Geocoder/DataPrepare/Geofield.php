<?php
/**
 * @file
 * The Geofield Data Prepare plugin.
 */

namespace Drupal\geocoder_geofield\Plugin\Geocoder\DataPrepare;

use Drupal\geocoder\Plugin\Geocoder\DataPrepareBase;
use Drupal\geocoder\Plugin\GeocoderPluginInterface;

/**
 * Class Geofield.
 *
 * @GeocoderPlugin(
 *  id = "data_prepare_geofield",
 *  name = "Geofield",
 *  field_types = {
 *     "geofield"
 *   }
 * )
 */
class Geofield extends DataPrepareBase implements GeocoderPluginInterface {

}
