<?php

/**
 * @file
 * Contains \Drupal\df_tools_user\Plugin\migrate\source\UserPictureFile.
 */

namespace Drupal\df_tools_user\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;

/**
 * Source for Image csv.
 *
 * @MigrateSource(
 *   id = "user_picture_file"
 * )
 */
class UserPictureFile extends CSV {

  public function prepareRow(Row $row) {
    if ($image = $row->getSourceProperty('Picture')) {
      $base_path = dirname($this->configuration['path']) . '/images/';

      // Setup our source/destination paths.
      $path = $base_path . $image;
      $destination_path = 'public://' . $image;

      // Copy the file.
      file_unmanaged_copy($path, $destination_path, FILE_EXISTS_REPLACE);

      $row->setSourceProperty('filepath', $path);
      $row->setDestinationProperty('uri', $destination_path);
      $row->setDestinationProperty('status', 1);
    }
  }

}
