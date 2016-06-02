<?php
/**
 * @file
 * The Text Data Prepare plugin.
 */

namespace Drupal\geocoder_field\Plugin\Geocoder\DataPrepare;

use Drupal\geocoder\Plugin\Geocoder\DataPrepareBase;
use Drupal\geocoder\Plugin\GeocoderPluginInterface;

/**
 * Class Text.
 *
 * @GeocoderPlugin(
 *  id = "data_prepare_text",
 *  name = "Text",
 *  field_types = {
 *     "string"
 *   }
 * )
 */
class Text extends DataPrepareBase implements GeocoderPluginInterface {

}
