<?php

namespace Drupal\df_tools_image\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CreateDerivativesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'df_tools_image_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $styles = [];

    foreach (\Drupal\image\Entity\ImageStyle::loadMultiple() as $name => $style) {
      $styles[$name] = $style->label();
    }

    $form['description'] = [
      '#markup' => '<p>' . t('Image derivatives can be created in bulk manually to remove the need for Drupal to create them on page load.') . '</p>',
    ];
    $form['image_styles'] = [
      '#type' => 'checkboxes',
      '#options' => $styles,
      '#title' => $this->t('Which derivatives would you like to create?'),
      '#description' => $this->t('Select all image styles that should be created. If none are selected, all image derivatives will be created.'),
    ];
    $form['run'] = [
      '#type' => 'submit',
      '#value' => t('Create derivatives'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $styles = array_diff(array_values($form_state->getValue('image_styles')), [0]);
    return df_tools_image_seed_derivatives('/', array(), $styles);
  }
}

