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
    if (($lat = $row->getSourceProperty('Lat')) && ($lng = $row->getSourceProperty('Long'))) {
      $location = array(
        'lat' => $lat,
        'lng' => $lng
      );
      $row->setSourceProperty('Location', $location);
    }
  }

}
