<?php
/**
 * @file
 * The FreeGeoIp plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class FreeGeoIp.
 *
 * @GeocoderPlugin(
 *  id = "freegeoip",
 *  name = "FreeGeoIp"
 * )
 */
class FreeGeoIp extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $this->setHandler(new \Geocoder\Provider\FreeGeoIp($this->getAdapter()));

    return parent::init();
  }

}
