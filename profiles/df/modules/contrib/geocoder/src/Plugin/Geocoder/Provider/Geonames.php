<?php
/**
 * @file
 * The Geonames plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class Geonames.
 *
 * @GeocoderPlugin(
 *  id = "geonames",
 *  name = "Geonames"
 * )
 */
class Geonames extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\Geonames($this->getAdapter(), $configuration['username']));

    return parent::init();
  }

}
