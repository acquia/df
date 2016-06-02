<?php
/**
 * @file
 * The TomTom plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class TomTom.
 *
 * @GeocoderPlugin(
 *  id = "tomtom",
 *  name = "TomTom"
 * )
 */
class TomTom extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\TomTom($this->getAdapter(), $configuration['apiKey']));

    return parent::init();
  }

}
