<?php

/**
 * @file
 * Contains \Drupal\geocoder_geofield\Plugin\Field\FieldFormatter\ReverseGeocodeGeofieldFormatter.
 */

namespace Drupal\geocoder_geofield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\geocoder\Geocoder;
use Drupal\geocoder_field\Plugin\Field\GeocodeFormatterBase;

/**
 * Plugin implementation of the Geocode formatter.
 *
 * @FieldFormatter(
 *   id = "geocoder_geofield_reverse_geocode",
 *   label = @Translation("Reverse geocode"),
 *   field_types = {
 *     "geofield",
 *   }
 * )
 */
class ReverseGeocodeGeofieldFormatter extends GeocodeFormatterBase {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $geophp = \Drupal::service('geophp.geophp');
    $dumper = \Drupal::service('geocoder.dumper.' . $this->getSetting('dumper_plugin'));
    $provider_plugins = $this->getEnabledProviderPlugins();

    foreach ($items as $delta => $item) {
      /** @var \Geometry $geom */
      $geom = $geophp->load($item->value);
      $centroid = $geom->getCentroid();
      if ($addressCollection = Geocoder::reverse($provider_plugins, $centroid->y(), $centroid->x())) {
        $elements[$delta] = array(
          '#markup' => $dumper->dump($addressCollection->first()),
        );
      }
    }

    return $elements;
  }

}
