<?php
/**
 * @file
 * The BingMaps plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class BingMaps.
 *
 * @GeocoderPlugin(
 *  id = "bingmaps",
 *  name = "BingMaps"
 * )
 */
class BingMaps extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\BingMaps($this->getAdapter(), $configuration['apiKey']));

    return parent::init();
  }

}
