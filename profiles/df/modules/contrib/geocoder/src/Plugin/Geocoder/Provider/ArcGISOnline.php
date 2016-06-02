<?php
/**
 * @file
 * The ArcGISOnline plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\Plugin\Geocoder\ProviderBase;
use Drupal\geocoder\Plugin\Geocoder\ProviderInterface;

/**
 * Class ArcGISOnline.
 *
 * @GeocoderPlugin(
 *  id = "arcgisonline",
 *  name = "ArcGISOnline"
 * )
 */
class ArcGISOnline extends ProviderBase implements ProviderInterface {
  /**
   * @inheritdoc
   */
  public function init() {
    $configuration = $this->getConfiguration();
    $this->setHandler(new \Geocoder\Provider\ArcGISOnline($this->getAdapter(), $configuration['sourceCountry']));

    return parent::init();
  }

}
