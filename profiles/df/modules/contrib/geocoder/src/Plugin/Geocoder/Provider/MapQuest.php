<?php
/**
 * @file
 * The MapQuest plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class MapQuest.
 *
 * @GeocoderPlugin(
 *  id = "mapquest",
 *  name = "MapQuest"
 * )
 */
class MapQuest extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\MapQuest($this->getAdapter(), $configuration['apiKey'], $configuration['licensed']));

    return parent::init();
  }

}
