<?php
/**
 * @file
 * The HostIp plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class HostIp.
 *
 * @GeocoderPlugin(
 *  id = "hostip",
 *  name = "HostIp"
 * )
 */
class HostIp extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $this->setHandler(new \Geocoder\Provider\HostIp($this->getAdapter()));

    return parent::init();
  }

}
