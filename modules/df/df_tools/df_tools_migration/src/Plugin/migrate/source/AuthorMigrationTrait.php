<?php

/**
 * @file
 * Contains \Drupal\df_tools_migration\Plugin\migrate\source\AuthorMigrationTrait.
 */

namespace Drupal\df_tools_migration\Plugin\migrate\source;

use Drupal\migrate\Row;

/**
 * Provides shared methods for processing image migrations in Source Plugins.
 */
trait AuthorMigrationTrait {

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
   * Sets the UID property for the migration target, optionally
   * accepting a null UID and role-based lookup with
   * random author assignment.
   *
   * @param \Drupal\migrate\Row $row
   * @param null $uid
   * @param string $roles
   */
  protected function setUidProperty(Row $row, $uid = null, $roles = 'administrator') {
    if ($uid == null) {
      $request_users = \Drupal::entityTypeManager()
        ->getStorage('user')
        ->loadByProperties(['roles' => $roles]);
      $uid = array_rand($request_users);
    }
    $row->setSourceProperty('UID', $uid);
  }

}
