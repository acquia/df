<?php

/**
 * Implements hook_views_data_alter().
 */
function df_tools_map_views_data_alter(array &$data) {
  // Provide a integration for each entity type except comment.
  $entity_manager = \Drupal::entityManager();
  $geofield_map = $entity_manager->getFieldMapByFieldType('geofield');
  foreach ($geofield_map as $entity_type_id => $fields) {
    foreach ($fields as $field_name => $info) {
      $args = array('@field_name' => $field_name);
      $data_key = $entity_type_id . '__' . $field_name;

      $field_coordinates_table_data = $data[$data_key][$field_name];
      $data[$data_key][$data_key . '_simple_proximity'] = [
        'group' => 'Content',
        'title' => t('Simple Proximity (@field_name)', $args),
        'title short' => $field_coordinates_table_data['title short'] . t(' Simple Proximity'),
        'help' => $field_coordinates_table_data['help'],
        'real field' => $field_name,
        'filter' => [
          'id' => 'df_tools_map_simple_proximity',
          'field_name' => $field_name,
          'entity_type' => $entity_type_id,
          'allow empty' => TRUE,
        ],
      ];
    }
  }
}
