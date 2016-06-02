<?php

/**
 * @file
 * Contains \Drupal\geocoder\Plugin\Geocoder\ProviderBase.
 */

namespace Drupal\geocoder\Plugin\Geocoder;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\geocoder\Plugin\GeocoderPluginBase;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 *
 */
abstract class ProviderBase extends GeocoderPluginBase implements ProviderInterface {
  /**
   * @var HttpAdapterInterface
   */
  private $adapter;

  /**
   * @var \Geocoder\Provider\Provider
   */
  private $handler;

  /**
   * {@inheritdoc}
   */
  public function __construct($configuration, $plugin_id, $plugin_definition, HttpAdapterInterface $adapter) {
    $this->adapter = $adapter;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('geocoder.http_adapter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setHandler(\Geocoder\Provider\Provider $handler) {
    $this->handler = $handler;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHandler() {
    return $this->handler;
  }

  /**
   * {@inheritdoc}
   */
  public function getAdapter() {
    return $this->adapter;
  }

  /**
   * {@inheritdoc}
   */
  public function geocode($data) {
    $cid = $this->getCacheCid($data);

    if ($value = $this->cache_get($cid)) {
      return $value;
    }

    try {
      $value = $this->getHandler()->geocode($data);
    }
    catch (\Exception $e) {
      throw $e;
    } catch (\InvalidCredentials $e) {
      throw $e;
    }

    $this->cache_set($cid, $value);

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function reverse($latitude, $longitude) {
    $cid = $this->getCacheCid($latitude, $longitude);

    if ($value = $this->cache_get($cid)) {
      return $value;
    }

    try {
      $value = $this->getHandler()->reverse($latitude, $longitude);
    }
    catch (\Exception $e) {
      throw $e;
    }

    $this->cache_set($cid, $value);

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function cache_get($cid) {
    if ((bool) \Drupal::config('geocoder.config')->get('cache')) {
      if ($cache = \Drupal::cache()->get($cid)) {
        return $cache->data;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function cache_set($cid, $data) {
    if ((bool) \Drupal::config('geocoder.config')->get('cache')) {
      \Drupal::cache()->set($cid, $data, CacheBackendInterface::CACHE_PERMANENT);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheCid() {
    $args = func_get_args();

    $args[] = $this->getPluginId();
    $args[] = $this->getConfiguration();

    return sha1(serialize(array_filter($args)));
  }

}
