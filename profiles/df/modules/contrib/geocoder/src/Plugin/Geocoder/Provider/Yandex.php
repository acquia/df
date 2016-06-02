<?php
/**
 * @file
 * The Yandex plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class Yandex.
 *
 * @GeocoderPlugin(
 *  id = "yandex",
 *  name = "Yandex"
 * )
 */
class Yandex extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $this->setHandler(new \Geocoder\Provider\Yandex($this->getAdapter()));

    return parent::init();
  }

}
