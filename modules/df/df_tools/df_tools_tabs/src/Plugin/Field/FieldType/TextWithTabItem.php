<?php

namespace Drupal\df_tools_tabs\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\text\Plugin\Field\FieldType\TextItemBase;

/**
 * Plugin implementation of the 'text_with_tab' field type.
 *
 * @FieldType(
 *   id = "text_with_tab",
 *   label = @Translation("Text (formatted, long, as tabs)"),
 *   description = @Translation("This field stores long text with a format and tab attributes."),
 *   category = @Translation("Text"),
 *   default_widget = "text_with_tab",
 *   default_formatter = "text_with_tab"
 * )
 */
class TextWithTabItem extends TextItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['tab_title'] = DataDefinition::create('string')
      ->setLabel(t('Tab Title'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'text',
          'size' => 'big',
        ],
        'tab_title' => [
          'type' => 'text',
          'size' => 'big',
        ],
        'format' => [
          'type' => 'varchar_ascii',
          'length' => 255,
        ],
      ],
      'indexes' => [
        'format' => ['format'],
      ],
    ];
  }

}
