<?php

/**
 * @file
 * Scenarios hooks implemented by the DFS One profile.
 */

/**
 * Implements hook_scenarios_info().
 */
function dfs_one_scenarios_info() {
  return [
    'dfs_one' => [
      'type' => 'profile',
      'label' => t('DFS ONE'),
      'description' => t('Demo Framework Scenario for One.'),
      //'files' => ['/files/images' => 'public://images'],
      'cdf' => ['/cdf'],
    ],
  ];
}
