<?php

namespace Drupal\df_tools_tabs\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'text_with_tab' formatter.
 *
 * @FieldFormatter(
 *   id = "text_with_tab",
 *   label = @Translation("Text with tab"),
 *   field_types = {
 *     "text_with_tab",
 *   },
 *   quickedit = {
 *     "editor" = "form"
 *   }
 * )
 */
class TextWithTabFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'container',
        '#attributes' => [
          'id' => $this->getFieldId($items, $delta),
        ],
      ];
      $elements[$delta][] = [
        '#type' => 'processed_text',
        '#text' => $item->value,
        '#format' => $item->format,
        '#langcode' => $item->getLangcode(),
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function view(FieldItemListInterface $items, $langcode = NULL) {
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['df-tools-tabs-wrapper']],
    ];

    $tabs = [
      '#theme' => 'item_list',
      '#items' => [],
      '#wrapper_attributes' => ['class' => ['df-tools-tabs-tabs', 'tabs']],
    ];

    foreach ($items as $delta => $item) {
      $url = Url::fromUri('internal:#' . $this->getFieldId($items, $delta))->toRenderArray();
      $tabs['#items'][] = [
        '#type' => 'link',
        '#title' => $item->tab_title,
      ] + $url;
    }

    $build[] = $tabs;
    $build[] = parent::view($items, $langcode);

    $build['#attached']['library'][] = 'df_tools_tabs/tabs';

    return $build;
  }

  /**
   * Forms a string representing a field's unique ID.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field values to be rendered.
   * @param int $delta
   *   The delta for the current item.
   *
   * @return string
   *   A string representing the field's ID
   */
  protected function getFieldId($items, $delta) {
    return implode('_', [$items->getEntity()->getEntityTypeId(), $items->getEntity()->id(), $this->fieldDefinition->getName(), $delta]);
  }

}
