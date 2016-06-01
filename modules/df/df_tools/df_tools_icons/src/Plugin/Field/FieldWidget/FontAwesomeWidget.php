<?php

/**
 * @file
 * Contains \Drupal\df_tools_icons\Plugin\Field\FieldWidget\FontAwesomeWidget.
 */

namespace Drupal\df_tools_icons\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'fontawesome_textfield' widget.
 *
 * @FieldWidget(
 *   id = "fontawesome_textfield",
 *   label = @Translation("Font Awesome Textfield"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class FontAwesomeWidget extends StringTextfieldWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['value']['#attributes']['class'][] = 'enable-fontawesome-iconpicker';
    $element['#attached']['library'][] = 'df_tools_icons/fontawesome_textfield';

    return $element;
  }

}
