<?php
/**
 * @file
 * The GeoPlugin plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class GeoPlugin.
 *
 * @GeocoderPlugin(
 *  id = "geoplugin",
 *  name = "GeoPlugin"
 * )
 */
class GeoPlugin extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $this->setHandler(new \Geocoder\Provider\GeoPlugin($this->getAdapter()));

    return parent::init();
  }

}
