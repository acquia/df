<?php
/**
 * @file
 * The MaxMind plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class MaxMind.
 *
 * @GeocoderPlugin(
 *  id = "maxmind",
 *  name = "MaxMind"
 * )
 */
class MaxMind extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\MaxMind($this->getAdapter(), $configuration['apiKey']));

    return parent::init();
  }

}
