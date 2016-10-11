<?php

/**
 * @file
 * Contains \Drupal\df_tools_user\Plugin\migrate\source\UserFromNodeTitle.
 */

namespace Drupal\df_tools_user\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;
/**
 * Source for multiple user roles via CSV.
 *
 * @MigrateSource(
 *   id = "user_from_node_title"
 * )
 */
class UserFromNodeTitle extends CSV {
  public function prepareRow(Row $row) {
    if ($value = $row->getSourceProperty('Title')) {
      $strings = explode(' ', $value);
      $first_name = $strings[0];
      $last_name = $strings[1];
      $password = \Drupal::service('password')->hash('password');
      $row->setSourceProperty('Name', $first_name . $last_name);
      $row->setSourceProperty('First', $first_name);
      $row->setSourceProperty('Last', $last_name);
      $row->setSourceProperty('Pass', $password);
      $row->setSourceProperty('Status', 1);
      $row->setSourceProperty('Mail', $first_name . $last_name . '@example.com');
    }
  }
}
