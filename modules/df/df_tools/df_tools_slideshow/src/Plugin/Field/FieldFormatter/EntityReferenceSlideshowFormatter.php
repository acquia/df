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
        'autoplay' => TRUE,
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

    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    foreach ($elements as $delta => $element) {
      // We need to re-render this in the right language.
      if (isset($element['#block_content'])) {
        $entity = $element['#block_content'];
        // @todo Investigate core bug and pull patch.
        $new_view = entity_view($entity, $this->getSetting('view_mode'), $langcode);
        if (isset($elements[$delta]['field_hero_image'])) {
          $new_view['field_hero_image'] = $elements[$delta]['field_hero_image'];
        }
        $elements[$delta] = $new_view;
      }
    }

    // For some reason preprocess_block doesn't get called for entity references to blocks
    foreach (Element::getVisibleChildren($elements) as $i) {
      if (isset ($elements[$i]['#block_content']) && $elements[$i]['#block_content']->bundle() == 'hero') {
        $wrapper = array(
          '#prefix' =>'<div class="full-width-inner">',
          '#suffix' =>'</div>',
          '#type' => 'container',
          '#weight' => -1,
          '#attributes' => array(
            'class' => array('hero-block-fields row align-center')
          ),
          '#children' => array()
        );
        foreach (Element::getVisibleChildren($elements[$i]) as $field_name) {
          if ($field_name != 'field_hero_image') {
            $wrapper['#children'][] = $elements[$i][$field_name];
            unset($elements[$i][$field_name]);
          }
        }
        $elements[$i]['wrapper'] = $wrapper;
      }
    }

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
