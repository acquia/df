<?php

/**
 * @file
 * Contains \Drupal\df_tools_migration\Plugin\migrate\source\ImageMigrationTrait.
 */

namespace Drupal\df_tools_migration\Plugin\migrate\source;

use Drupal\migrate\Row;

/**
 * Provides shared methods for processing image migrations in Source Plugins.
 */
trait ImageMigrationTrait {

  /**
   * Configuration information passed into the plugin.
   *
   * @var array
   */
  protected $configuration;

  /**
   * The entity migration object.
   *
   * @var \Drupal\migrate\Entity\MigrationInterface
   */
  protected $migration;


  /**
   * Sets an image field and alt text in a migration based on the source
   * property (the name of the file).
   *
   * @param \Drupal\migrate\Row $row
   * @param string $source_property
   */
  protected function setImageProperty(Row $row, $source_property) {
    if ($value = $row->getSourceProperty($source_property)) {
      $path = dirname($this->configuration['path']) . '/images/' . $value;
      $data = file_get_contents($path);
      $uri = file_build_uri($value);
      $file = file_save_data($data, $uri);

      // Calculate alt text based on the filename.
      $alt_text = ucwords(str_replace(['_', '-'], ' ', preg_replace('/\..*/', '', $value)));

      $item = [[
        'target_id' => $file->id(),
        'alt' => $alt_text
      ]];

      $row->setSourceProperty($source_property, $item);
    }
  }

}
