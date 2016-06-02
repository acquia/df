<?php

/**
 * @file
 * Contains Drupal\geocoder\Component\Annotation\GeocoderPlugin.
 */

namespace Drupal\geocoder\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Geocoder Plugin annotation object.
 *
 * @ingroup plugin_api
 *
 * @Annotation
 */
class GeocoderPlugin extends Plugin {

  public $type;

}
