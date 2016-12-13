<?php

/**
 * @file
 * Contains df_tools_panelizer.drush.inc.
 */

/**
 * Implements hook_drush_command().
 */
function df_tools_panelizer_drush_command() {
  $items = array();

  $items['df-panelizer-export'] = array(
    'description' => 'Exports a given Node\'s Panelizer Display to a scenario config file.',
    'arguments' => array(
      'nid' => 'The ID of the override Node.',
      'scenario' => 'The target Scenario.',
    ),
    'examples' => array(
      'df-pe 1 dfs_tec' => 'Export the Panels Display of Node 1 to the given scenario\'s config directory.',
    ),
    'drupal dependencies' => array('panelizer', 'df_tools_panelizer'),
    'aliases' => array('df-pe'),
  );

  return $items;
}

/**
 * Exports a given Node's Panels Display to a file in a given scenario.
 *
 * @param string $nid
 * @param string $scenario
 *
 * @return bool
 */
function drush_df_tools_panelizer_df_panelizer_export($nid, $scenario) {
  // Load the Node.
  $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

  // Check if it is Panelized.
  if ($node->panelizer) {
    // Get the path to the current scenario.
    $base_path = drupal_get_path('module', $scenario);
    if (!$base_path) {
      drupal_set_message(t('The scenario @scenario is not installed.', ['@scenario' => $scenario]), 'error');
      return FALSE;
    }

    // Write YAML to a file.
    $panels_file = $base_path . '/data/panelizer.panels_display.node.'.$node->uuid() . '.yml';
    $yaml = \Symfony\Component\Yaml\Yaml::dump($node->panelizer->panels_display, 99, 2);
    file_put_contents($panels_file, $yaml);

    drupal_set_message(t('Successfully exported Panelizer display for Node @nid to @panels_file', ['@nid' => $node->id(), '@panels_file' => $panels_file]));
    return TRUE;
  }
  else {
    drupal_set_message(t('The given Node is not Panelized.'), 'error');
    return FALSE;
  }
}
