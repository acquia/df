<?php
/**
 * @file
 * The AddressText plugin.
 */

namespace Drupal\geocoder_address\Plugin\Geocoder\Dumper;

use Drupal\geocoder\Plugin\Geocoder\DumperBase;
use Drupal\geocoder\Plugin\Geocoder\DumperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AddressText.
 *
 * @GeocoderPlugin(
 *  id = "addresstext",
 *  name = "Address string"
 * )
 */
class AddressText extends DumperBase implements DumperInterface {
  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('geocoder.dumper.addresstext')
    );
  }

}
