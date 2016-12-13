<?php

/**
 * @file
 * Contains \Drupal\df_tools_migration\Plugin\migrate\source\CreatorNode.
 */

namespace Drupal\df_tools_migration\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;

/**
 * Source for any old node via CSV with a hard-coded Creator author.
 *
 * @MigrateSource(
 *   id = "creator_node"
 * )
 */
class CreatorNode extends CSV {

  use AuthorMigrationTrait;

  public function prepareRow(Row $row) {
    $this->setUidProperty($row, null, 'creator');
  }

}

