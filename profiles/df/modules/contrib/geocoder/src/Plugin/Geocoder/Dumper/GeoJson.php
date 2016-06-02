<?php
/**
 * @file
 * The GeoJson plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Dumper;

use Drupal\geocoder\Plugin\Geocoder\DumperInterface;
use Drupal\geocoder\Plugin\Geocoder\DumperBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeoJson.
 *
 * @GeocoderPlugin(
 *  id = "geojson",
 *  name = "GeoJson"
 * )
 */
class GeoJson extends DumperBase implements DumperInterface {
  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('geocoder.dumper.geojson')
    );
  }

}
