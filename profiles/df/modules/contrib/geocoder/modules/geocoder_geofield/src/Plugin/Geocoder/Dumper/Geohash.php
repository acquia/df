<?php
/**
 * @file
 * The Geohash plugin.
 */

namespace Drupal\geocoder_geofield\Plugin\Geocoder\Dumper;

use Drupal\geocoder\Plugin\Geocoder\Dumper\GeoJson;
use Drupal\geocoder\Plugin\Geocoder\DumperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Geohash.
 *
 * @GeocoderPlugin(
 *  id = "geohash",
 *  name = "Geohash"
 * )
 */
class Geohash extends GeoJson implements DumperInterface {
  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('geocoder.dumper.geohash')
    );
  }

}
