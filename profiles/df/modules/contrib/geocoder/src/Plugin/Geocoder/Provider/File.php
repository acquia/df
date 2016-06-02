<?php
/**
 * @file
 * The File plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class File.
 *
 * @GeocoderPlugin(
 *  id = "file",
 *  name = "File"
 * )
 */
class File extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $this->setHandler(new \Drupal\geocoder\Geocoder\Provider\File());

    return parent::init();
  }

}
