<?php
/**
 * @file
 * The OpenStreetMap plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class OpenStreetMap.
 *
 * @GeocoderPlugin(
 *  id = "openstreetmap",
 *  name = "OpenStreetMap"
 * )
 */
class OpenStreetMap extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $this->setHandler(new \Geocoder\Provider\OpenStreetMap($this->getAdapter()));

    return parent::init();
  }

}
