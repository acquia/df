<?php
/**
 * @file
 * The IpInfoDb plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class IpInfoDb.
 *
 * @GeocoderPlugin(
 *  id = "ipinfodb",
 *  name = "IpInfoDb"
 * )
 */
class IpInfoDb extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\IpInfoDb($this->getAdapter(), $configuration['apiKey']));

    return parent::init();
  }

}
