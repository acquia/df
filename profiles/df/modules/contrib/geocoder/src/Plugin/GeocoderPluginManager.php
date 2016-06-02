<?php

/**
 * @file
 * Contains \Drupal\geocoder\Plugin\GeocoderPluginManager.
 */

namespace Drupal\geocoder\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * Plugin type manager for all Geocoder plugins.
 */
class GeocoderPluginManager extends DefaultPluginManager {
  /**
   * @inheritdoc
   */
  public function __construct($type, \Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    $plugin_definition_annotation_name = 'Drupal\geocoder\Annotation\GeocoderPlugin';
    parent::__construct('Plugin/Geocoder/' . Container::camelize($type), $namespaces, $module_handler, 'Drupal\geocoder\Plugin\GeocoderPluginInterface', $plugin_definition_annotation_name);

    $this->defaults += array(
      'plugin_type' => strtolower($type),
    );

    $this->alterInfo('geocoder_plugins_' . $type);
    $this->setCacheBackend($cache_backend, 'geocoder:' . $type);
  }

}
