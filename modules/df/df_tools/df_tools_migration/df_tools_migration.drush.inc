<?php

/**
 * @file
 * Contains df_tools_migration.drush.inc.
 */

use Drush\Log\LogLevel;
use Symfony\Component\Yaml\Yaml;

/**
 * Implements hook_drush_command().
 */
function df_tools_migration_drush_command() {
  $items = array();

  $items['df-migration-export'] = array(
    'description' => 'Exports a given migration as YML config',
    'arguments' => array(
      'name' => 'The migration plugin name',
      'destination' => 'The target destination; defaults to public://',
    ),
    'examples' => array(
      'df-me import_node_article public://migrations/' => 'Export the configuration for an Article node migration to public://migrations/import_node_article.yml',
    ),
    'drupal dependencies' => array('migrate', 'df_tools_migration'),
    'aliases' => array('df-me'),
  );

  return $items;
}

/**
 * @param $name
 * @param string $destination
 * @return bool
 */
function drush_df_tools_migration_df_migration_export($name, $destination = "public://") {
  $plugin_manager_migration = \Drupal::service('plugin.manager.migration');
  $plugin_manager_migration->clearCachedDefinitions();
  if (!$migration = $plugin_manager_migration->createInstance($name)) {
    $plugin_manager_config_entity_migration = \Drupal::service('plugin.manager.config_entity_migration');
    if (!$migration = $plugin_manager_config_entity_migration->createInstance($name)) {
      $message = t('Migration @name is a config entity.', ['@name' => $name]);
      drush_log($message, LogLevel::NOTICE);
    }
  }
  if ($migration != null) {
    $definition = $migration->getPluginDefinition();
    $dump = Yaml::dump($definition);
    $file_url = $destination . $migration->id() . '.yml';
    $message = t('Export migration @name?', ['@name' => $name]);
    if (drush_confirm($message) && $data = file_save_data($dump, $file_url)) {
      drush_print(t('File saved: @file', ['@file' => $data->getFileUri()]));
      return TRUE;
    }
  }
  return FALSE;
}
