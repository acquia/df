<?php

/**
 * @file
 * Contains \Drupal\geocoder\Plugin\Geocoder\ProviderInterface.
 */

namespace Drupal\geocoder\Plugin\Geocoder;

use Drupal\geocoder\Plugin\GeocoderPluginInterface;
/**
 *
 */
interface ProviderInterface extends GeocoderPluginInterface {
  /**
   * Geocode data.
   *
   * @param string $data
   *   The data to geocode.
   *
   * @return AddressCollection|FALSE
   */
  public function geocode($data);

  /**
   * Reverse geocode latitude and longitude.
   *
   * @param float $latitude
   *   The latitude
   * @param float $longitude
   *   The longitude
   *
   * @return AddressCollection|FALSE
   */
  public function reverse($latitude, $longitude);

  /**
   * Set the Geocoder handler to use.
   *
   * @param \Geocoder\Provider\Provider $handler
   *   The handler
   *
   * @return ProviderInterface
   */
  public function setHandler(\Geocoder\Provider\Provider $handler);

  /**
   * Get the Geocoder handler.
   *
   * @return \Geocoder\Provider\Provider
   */
  public function getHandler();

  /**
   * Returns the HTTP adapter.
   *
   * @return HttpAdapterInterface
   */
  public function getAdapter();

}
