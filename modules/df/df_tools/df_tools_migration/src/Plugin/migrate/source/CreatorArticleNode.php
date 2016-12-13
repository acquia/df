<?php

/**
 * @file
 * Contains \Drupal\df_tools_migration\Plugin\migrate\source\CreatorArticleNode.
 */

namespace Drupal\df_tools_migration\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;

/**
 * Source for Article node via CSV with a coded Creator author.
 *
 * @MigrateSource(
 *   id = "creator_article_node"
 * )
 */
class CreatorArticleNode extends CSV {

  use AuthorMigrationTrait;
  use ImageMigrationTrait;

  public function prepareRow(Row $row) {
    $this->setImageProperty($row, 'Image');
    $this->setUidProperty($row, null, 'creator');
  }
}

