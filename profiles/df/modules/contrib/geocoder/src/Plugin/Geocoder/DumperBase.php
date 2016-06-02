<?php

/**
 * @file
 * Contains \Drupal\geocoder\Plugin\Geocoder\DumperBase.
 */

namespace Drupal\geocoder\Plugin\Geocoder;

use Drupal\geocoder\Plugin\GeocoderPluginBase;
use Geocoder\Model\Address;
/**
 *
 */
abstract class DumperBase extends GeocoderPluginBase implements DumperInterface {
  /**
   * The Geocoder Dumper object.
   *
   * @var \Geocoder\Dumper\Dumper
   */
  private $geocoder_dumper;

  /**
   * @inheritdoc
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, \Geocoder\Dumper\Dumper $dumper) {
    $this->setGeocoderDumper($dumper);
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * @inheritdoc
   */
  public function dump(Address $address) {
    return $this->getGeocoderDumper()->dump($address);
  }

  /**
   * @inheritdoc
   */
  public function setGeocoderDumper(\Geocoder\Dumper\Dumper $dumper) {
    $this->geocoder_dumper = $dumper;
  }

  /**
   * @inheritdoc
   */
  public function getGeocoderDumper() {
    return $this->geocoder_dumper;
  }

}
