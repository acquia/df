<?php

/**
 * @file
 */

namespace Drupal\geocoder\Geocoder\Provider;

use Geocoder\Exception\NoResult;
use Geocoder\Exception\UnsupportedOperation;
use Geocoder\Provider\AbstractProvider;
use Geocoder\Provider\Provider;

/**
 * @author Pol Dellaiera <pol.dellaiera@gmail.com>
 */
class File extends AbstractProvider implements Provider {
  /**
   * {@inheritDoc}.
   */
  public function geocode($filename) {
    if ($exif = exif_read_data($filename)) {
      if (isset($exif['GPSLatitude']) && isset($exif['GPSLatitudeRef']) && $exif['GPSLongitude'] && $exif['GPSLongitudeRef']) {
        $latitude = $this->getGPSExif($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
        $longitude = $this->getGPSExif($exif['GPSLongitude'], $exif['GPSLongitudeRef']);

        return $this->returnResults([
          array_merge($this->getDefaults(), [
            'latitude' => $latitude,
            'longitude' => $longitude,
          ]),
        ]);
      }
    }

    throw new NoResult(sprintf('Could not find geo data in file: "%s".', basename($filename)));
  }

  /**
   * Helper function to retrieve latitude and longitude data from exif.
   *
   * @param $coordinate
   * @param $hemisphere
   *
   * @return float
   */
  public function getGPSExif($coordinate, $hemisphere) {
    for ($i = 0; $i < 3; $i++) {
      $part = explode('/', $coordinate[$i]);

      if (count($part) == 1) {
        $coordinate[$i] = $part[0];
      }
      elseif (count($part) == 2) {
        $coordinate[$i] = floatval($part[0]) / floatval($part[1]);
      }
      else {
        $coordinate[$i] = 0;
      }
    }

    list($degrees, $minutes, $seconds) = $coordinate;
    $sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;
    $value = $sign * ($degrees + $minutes / 60 + $seconds / 3600);

    return $value;
  }

  /**
   * {@inheritDoc}.
   */
  public function reverse($latitude, $longitude) {
    throw new UnsupportedOperation('The Image plugin is not able to do reverse geocoding.');
  }

  /**
   * {@inheritDoc}.
   */
  public function getName() {
    return 'file';
  }

}
