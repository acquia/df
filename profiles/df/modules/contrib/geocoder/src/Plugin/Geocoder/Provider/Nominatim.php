<?php
/**
 * @file
 * The Nominatim plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class Nominatim.
 *
 * @GeocoderPlugin(
 *  id = "nominatim",
 *  name = "Nominatim"
 * )
 */
class Nominatim extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\Nominatim($this->getAdapter(), $configuration['rootUrl']));

    return parent::init();
  }

}
