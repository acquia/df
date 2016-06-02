<?php
/**
 * @file
 * The Kml plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Dumper;

use Drupal\geocoder\Plugin\Geocoder\DumperBase;
use Drupal\geocoder\Plugin\Geocoder\DumperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Kml.
 *
 * @GeocoderPlugin(
 *  id = "kml",
 *  name = "KML"
 * )
 */
class Kml extends DumperBase implements DumperInterface {
  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('geocoder.dumper.kml')
    );
  }

}
