<?php

/**
 * @file
 * Contains \Drupal\df_tools_slideshow\Plugin\Field\FieldFormatter\EntityReferenceSlideshowFormatter.
 */

namespace Drupal\df_tools_slideshow\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Renders any entity reference field as a slideshow of rendered entities.
 *
 * @FieldFormatter(
 *   id = "entity_reference_slideshow",
 *   label = @Translation("Rendered entity as Slideshow"),
 *   description = @Translation("Display the referenced entities rendered by entity_view() as slides in a slideshow."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class EntityReferenceSlideshowFormatter extends EntityReferenceEntityFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'view_mode' => 'default',
      'link' => FALSE,
      'slick' => array(
        'arrows' => TRUE,
        'draggable' => TRUE,
        'dots' => TRUE,
        'accessibility' => TRUE,
        'adaptiveHeight' => FALSE,
        'variableWidth' => FALSE,
        'autoplay' => FALSE,
        'centerMode' => FALSE,
        'infinite' => TRUE,
        'useCSS' => TRUE,
        'mobileFirst' => FALSE,
        'rtl' => FALSE,
        'fade' => FALSE,
        'pauseOnDotsHover' => FALSE,
        'vertical' => FALSE
      )
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];
    $elements['view_mode'] = array(
      '#type' => 'select',
      '#options' => \Drupal::entityManager()->getViewModeOptions($this->getFieldSetting('target_type')),
      '#title' => t('View mode'),
      '#default_value' => $this->getSetting('view_mode'),
      '#required' => TRUE,
    );

    $elements['slick'] = array(
      '#type' => 'fieldset',
      '#title' => t('Slick settings'),
      '#description' => t('Visit @url to view detailed descriptions for each setting.', array('@url' => 'http://kenwheeler.github.io/slick/#settings')),
    );

    foreach ($this->getSetting('slick') as $setting => $default_value) {
      $elements['slick'][$setting] = array(
        '#type' => 'checkbox',
        '#title' => $setting,
        '#default_value' => $default_value,
      );
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    $elements['#attached']['library'][] = 'df_tools_slideshow/main';

    $elements['#attributes']['class'][] = 'df-tools-slideshow';

    // Fix slick boolean values so that they're passed to javascript correctly
    $slick = array();
    foreach ($this->getSetting('slick') as $setting => $value) {
      $slick[$setting] = (bool) $value;
    }

    $elements['#attached']['drupalSettings']['DFToolsSlideshow']['slick'] = $slick;

    return $elements;
  }

}
