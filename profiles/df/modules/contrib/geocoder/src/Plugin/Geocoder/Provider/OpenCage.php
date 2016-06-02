<?php
/**
 * @file
 * The OpenCage plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class OpenCage.
 *
 * @GeocoderPlugin(
 *  id = "opencage",
 *  name = "OpenCage"
 * )
 */
class OpenCage extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\OpenCage($this->getAdapter(), $configuration['apiKey']));

    return parent::init();
  }

}
