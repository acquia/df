<?php
/**
 * @file
 * The Geoip plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class Geoip.
 *
 * @GeocoderPlugin(
 *  id = "geoip",
 *  name = "Geoip"
 * )
 */
class Geoip extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $this->setHandler(new \Geocoder\Provider\Geoip());

    return parent::init();
  }

}
