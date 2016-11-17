<?php

/**
 * @file
 * Contains \Drupal\import\Plugin\migrate\source\ImageFile.
 */

namespace Drupal\df_tools_media\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;

/**
 * Source for the Media Image csv.
 *
 * @MigrateSource(
 *   id = "media_image"
 * )
 */
class MediaImage extends CSV {

  public function prepareRow(Row $row) {
    if ($image = $row->getSourceProperty('Image')) {
      $files = \Drupal::entityTypeManager()
        ->getStorage('file')->loadByProperties(['filename' => $image]);

      if (empty($files)) {
        $base_path = dirname($this->configuration['path']) . '/images/';
        $path = $base_path . $image;
        $data = file_get_contents($path);
        $uri = file_build_uri($image);
        $file = file_save_data($data, $uri);
      }
      else {
        $file = reset($files);
      }

      $row->setSourceProperty('File', $file);
    }
  }

}
