<?php

/**
 * @file
 * Contains \Drupal\df_tools_blocks\Plugin\migrate\source\MediaBlock.
 */

namespace Drupal\df_tools_blocks\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;

/**
 * Source for Media block CSV.
 *
 * @MigrateSource(
 *   id = "media_block"
 * )
 */
class MediaBlock extends CSV {

  public function prepareRow(Row $row) {
  }

}
