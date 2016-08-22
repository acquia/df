<?php

namespace Drupal\df_tools_map\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;

/**
 * Source for Map block CSV.
 *
 * @MigrateSource(
 *   id = "map_block"
 * )
 */
class MapBlock extends CSV {

  public function prepareRow(Row $row) {
    // Prefix the state per the normal Address schema.
    if ($state = $row->getSourceProperty('Address State')) {
      $row->setSourceProperty('Address State', 'US-' . $state);
    }

    // Use a cached geolocation if possible.
    if ($geolocation = $row->getSourceProperty('Geolocation')) {
      list($lat, $lon) = explode(',', $geolocation);
      $point = [$lon, $lat];
      $row->setSourceProperty('Geofield', \Drupal::service('geofield.wkt_generator')->WktBuildPoint($point));
    }
    else {
      // Manually geocode from source information.
      $address = $row->getSourceProperty('Address Line 1');
      $address2 = $row->getSourceProperty('Address Line 2');
      $city = $row->getSourceProperty('Address City');
      $zip = $row->getSourceProperty('Address Zip');

      if ($address && $city && $zip && $state) {
        $address2 = $address2 ? $address2 . "\n" : '';
        $address_string = "$address\n$address2$city, $state $zip\nUS";
        if ($collection = \Drupal::service('geocoder')->geocode($address_string, ['googlemaps'])) {
          // Set our value in a similar way to Geofield's LatLon Widget.
          // @see \Drupal\geofield\Plugin\Field\FieldWidget\GeofieldLatLonWidget::massageFormValues()
          $coordinates = $collection->first()->getCoordinates();
          $point = [
            $coordinates->getLongitude(),
            $coordinates->getLatitude()
          ];
          $row->setSourceProperty('Geofield', \Drupal::service('geofield.wkt_generator')
            ->WktBuildPoint($point));
        }
      }
    }
  }

}
