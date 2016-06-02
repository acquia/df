<?php
/**
 * @file
 * The Gpx plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Dumper;

use Drupal\geocoder\Plugin\Geocoder\DumperBase;
use Drupal\geocoder\Plugin\Geocoder\DumperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Gpx.
 *
 * @GeocoderPlugin(
 *  id = "gpx",
 *  name = "GPX"
 * )
 */
class Gpx extends DumperBase implements DumperInterface {
  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('geocoder.dumper.gpx')
    );
  }

}
