<?php

namespace Drupal\df_tools_tabs\Plugin\Field\FieldWidget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\text\Plugin\Field\FieldWidget\TextareaWidget;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'text_with_tab' widget.
 *
 * @FieldWidget(
 *   id = "text_with_tab",
 *   label = @Translation("Text with tab"),
 *   field_types = {
 *     "text_with_tab"
 *   }
 * )
 */
class TextWithTabWidget extends TextareaWidget {

  /**
   * {@inheritdoc}
   */
  function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['tab_title'] = [
      '#type' => 'textfield',
      '#default_value' => $items[$delta]->tab_title,
      '#title' => $this->t('Tab Title'),
      '#description' => $this->t('The tab title.'),
      '#weight' => -10,
    ];

    return $element;
  }

}
