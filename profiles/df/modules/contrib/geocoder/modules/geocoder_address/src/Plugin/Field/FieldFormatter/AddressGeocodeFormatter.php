<?php

/**
 * @file
 * Contains \Drupal\geocoder_field\Plugin\Field\FieldFormatter\AddressGeocodeFormatter.
 */

namespace Drupal\geocoder_address\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\geocoder\Geocoder;
use Drupal\geocoder_field\Plugin\Field\FieldFormatter\GeocodeFormatter;

/**
 * Plugin implementation of the Geocode formatter.
 *
 * @FieldFormatter(
 *   id = "geocoder_address_geocode_formatter",
 *   label = @Translation("Geocode address"),
 *   field_types = {
 *     "address",
 *   }
 * )
 */
class AddressGeocodeFormatter extends GeocodeFormatter {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $dumper = \Drupal::service('geocoder.dumper.' . $this->getSetting('dumper_plugin'));

    foreach ($items as $delta => $item) {
      $value = $item->getValue();
      $address = array();

      $address[] = !empty($value['address_line1']) ? $value['address_line1'] : NULL;
      $address[] = !empty($value['address_line2']) ? $value['address_line2'] : NULL;
      $address[] = !empty($value['postal_code']) ? $value['postal_code'] : NULL;
      $address[] = !empty($value['locality']) ? $value['locality'] : NULL;
      $address[] = !empty($value['country']) ? $value['country'] : NULL;

      if ($addressCollection = Geocoder::geocode($this->getEnabledProviderPlugins(), implode(',', array_filter($address)))) {
        $elements[$delta] = array(
          '#plain_text' => $dumper->dump($addressCollection->first()),
        );
      }
    }

    return $elements;
  }

}
