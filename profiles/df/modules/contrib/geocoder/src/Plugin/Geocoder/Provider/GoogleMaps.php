<?php
/**
 * @file
 * The GoogleMaps plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class GoogleMaps.
 *
 * @GeocoderPlugin(
 *  id = "googlemaps",
 *  name = "GoogleMaps"
 * )
 */
class GoogleMaps extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $this->setHandler(new \Geocoder\Provider\GoogleMaps($this->getAdapter()));

    return parent::init();
  }

}
