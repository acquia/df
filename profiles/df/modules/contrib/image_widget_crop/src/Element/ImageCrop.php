<?php

namespace Drupal\image_widget_crop\Element;

use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\crop\Entity\Crop;
use Drupal\file\FileInterface;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Drupal\file_entity\Entity\FileEntity;

/**
 * Provides a form element for crop.
 *
 * @FormElement("image_crop")
 */
class ImageCrop extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#process' => [
        [static::class, 'processCrop'],
      ],
      '#file' => NULL,
      '#crop_preview_image_style' => 'crop_thumbnail',
      '#crop_type_list' => [],
      '#warn_multiple_usages' => FALSE,
      '#show_default_crop' => TRUE,
      '#attached' => [
        'library' => 'image_widget_crop/cropper.integration',
      ],
      '#tree' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $return = [];
    if ($input) {
      return $input;
    }
    return $return;
  }

  /**
   * Render API callback: Expands the image_crop element type.
   */
  public static function processCrop(&$element, FormStateInterface $form_state, &$complete_form) {
    /** @var \Drupal\file\Entity\File $file */
    $file = $element['#file'];
    if (!empty($file) && preg_match('/image/', $file->getMimeType())) {
      $element['#attached']['drupalSettings']['crop_default'] = $element['#show_default_crop'];

      // Display an error message if the local/remote library and CSS are not
      // set.
      $config = \Drupal::config('image_widget_crop.settings');
      $js_library = $config->get('settings.library_url');
      if (!\Drupal::moduleHandler()->moduleExists('libraries')) {
        if (empty($js_library)) {
          $element['message'] = [
            '#type' => 'container',
            '#markup' => t('Either set the library locally (in /libraries/cropper) and enable the libraries module or enter the remote URL on <a href="@link">Image Crop Widget settings</a>.', [
              '@link' => Url::fromRoute('image_widget_crop.crop_widget_settings')
                ->toString(),
            ]),
            '#attributes' => [
              'class' => ['messages messages--error'],
            ],
          ];
        }
      }

      /** @var \Drupal\Core\Image\Image $image */
      $image = \Drupal::service('image.factory')->get($file->getFileUri());
      if (!$image->isValid()) {
        $element['message'] = [
          '#type' => 'container',
          '#markup' => t('The file "@file" is not valid on element @name.', [
            '@file' => $file->getFileUri(),
            '@name' => $element['#name'],
          ]),
          '#attributes' => [
            'class' => ['messages messages--error'],
          ],
        ];
        // Stop image_crop process and display error message.
        return $element;
      }

      $crop_type_list = $element['#crop_type_list'];
      $element['crop_wrapper'] = [
        '#type' => 'details',
        '#title' => t('Crop image'),
        '#attributes' => ['class' => ['image-data__crop-wrapper']],
        '#weight' => 100,
      ];

      if ($element['#warn_multiple_usages']) {
        // Warn the user if the crop is used more than once.
        $usage_counter = self::countFileUsages($file);
        if ($usage_counter > 1) {
          $element['crop_reuse'] = [
            '#type' => 'container',
            '#markup' => t('This crop definition affects more usages of this image'),
            '#attributes' => [
              'class' => ['messages messages--warning'],
            ],
            '#weight' => -10,
          ];
        }
      }

      // Ensure that the ID of an element is unique.
      $list_id = \Drupal::service('uuid')->generate();

      $element['crop_wrapper'][$list_id] = [
        '#type' => 'vertical_tabs',
        '#theme_wrappers' => ['vertical_tabs'],
        '#parents' => [$list_id],
      ];

      /** @var \Drupal\Core\Config\Entity\ConfigEntityStorage $crop_type_storage */
      $crop_type_storage = \Drupal::entityTypeManager()->getStorage('crop_type');
      if (!empty($crop_type_storage->loadMultiple())) {
        foreach ($crop_type_list as $crop_type) {
          /** @var \Drupal\crop\Entity\CropType $crop_type */
          $crop_type = $crop_type_storage->load($crop_type);
          $ratio = $crop_type->getAspectRatio() ? $crop_type->getAspectRatio() : 'Nan';

          $element['#attached']['drupalSettings']['image_widget_crop'][$crop_type->id()] = [
            'soft_limit' => $crop_type->getSoftLimit(),
            'hard_limit' => $crop_type->getHardLimit(),
          ];

          $element['crop_wrapper'][$crop_type->id()] = [
            '#type' => 'details',
            '#title' => $crop_type->label(),
            '#group' => $list_id,
          ];

          // Generation of html List with image & crop information.
          $element['crop_wrapper'][$crop_type->id()]['crop_container'] = [
            '#type' => 'container',
            '#attributes' => [
              'class' => ['crop-preview-wrapper', $list_id],
              'id' => [$crop_type->id()],
              'data-ratio' => [$ratio],
            ],
            '#weight' => -10,
          ];

          $element['crop_wrapper'][$crop_type->id()]['crop_container']['image'] = [
            '#theme' => 'image_style',
            '#style_name' => $element['#crop_preview_image_style'],
            '#attributes' => [
              'class' => ['crop-preview-wrapper__preview-image'],
              'data-ratio' => $ratio,
              'data-name' => $crop_type->id(),
              'data-original-width' => ($file instanceof FileEntity) ? $file->getMetadata('width') : getimagesize($file->getFileUri())[0],
              'data-original-height' => ($file instanceof FileEntity) ? $file->getMetadata('height') : getimagesize($file->getFileUri())[1],
            ],
            '#uri' => $file->getFileUri(),
            '#weight' => -10,
          ];

          $element['crop_wrapper'][$crop_type->id()]['crop_container']['reset'] = [
            '#type' => 'button',
            '#value' => t('Reset crop'),
            '#attributes' => ['class' => ['crop-preview-wrapper__crop-reset']],
            '#weight' => -10,
          ];

          // Generation of html List with image & crop information.
          $element['crop_wrapper'][$crop_type->id()]['crop_container']['values'] = [
            '#type' => 'container',
            '#attributes' => ['class' => ['crop-preview-wrapper__value']],
            '#weight' => -9,
          ];

          // Element to track whether cropping is applied or not.
          $element['crop_wrapper'][$crop_type->id()]['crop_container']['values']['crop_applied'] = [
            '#type' => 'hidden',
            '#attributes' => ['class' => ["crop-applied"]],
            '#default_value' => 0,
          ];
          $edit = FALSE;
          $properties = [];
          $form_state_element_values = $form_state->getValue($element['#parents']);
          // Check if form state has values.
          if ($form_state_element_values) {
            $form_state_properties = $form_state_element_values['crop_wrapper'][$crop_type->id()]['crop_container']['values'];
            // If crop is applied by the form state we keep it that way.
            if ($form_state_properties['crop_applied'] == '1') {
              $element['crop_wrapper'][$crop_type->id()]['crop_container']['values']['crop_applied']['#default_value'] = 1;
              $edit = TRUE;
            }
            $properties = $form_state_properties;
          }

          /** @var \Drupal\crop\Entity\Crop $crop */
          $crop = Crop::findCrop($file->getFileUri(), $crop_type->id());
          if ($crop) {
            $edit = TRUE;
            /** @var \Drupal\image_widget_crop\ImageWidgetCropManager $image_widget_crop_manager */
            $image_widget_crop_manager = \Drupal::service('image_widget_crop.manager');
            $original_properties = $image_widget_crop_manager->getCropProperties($crop);

            // If form state values have the same values that were saved or if
            // form state has no values yet and there are saved values then we
            // use the saved values.
            $properties = $original_properties == $properties || empty($properties) ? $original_properties : $properties;
            $element['crop_wrapper'][$crop_type->id()]['crop_container']['values']['crop_applied']['#default_value'] = 1;
            // If the user edits an entity and while adding new images resets an
            // saved crop we keep it reset.
            if (isset($properties['crop_applied']) && $properties['crop_applied'] == '0') {
              $element['crop_wrapper'][$crop_type->id()]['crop_container']['values']['crop_applied']['#default_value'] = 0;
            }
          }
          self::getCropFormElement($element, 'crop_container', $properties, $edit, $crop_type->id());
        }
        // Stock Original File Values.
        $element['file-uri'] = [
          '#type' => 'value',
          '#value' => $file->getFileUri(),
        ];

        $element['file-id'] = [
          '#type' => 'value',
          '#value' => $file->id(),
        ];
      }
    }
    return $element;
  }

  /**
   * Counts how many times a file has been used.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity to check usages.
   *
   * @return int
   *   Returns how many times the file has been used.
   */
  public static function countFileUsages(FileInterface $file) {
    $counter = 0;
    $file_usage = \Drupal::service('file.usage')->listUsage($file);
    foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($file_usage)) as $usage) {
      $counter += (int) $usage;
    }
    return $counter;
  }

  /**
   * Inject crop elements into the form.
   *
   * @param array $element
   *   All form elements.
   * @param string $element_wrapper_name
   *   Name of element contains all crop properties.
   * @param array $original_properties
   *   All properties calculate for apply to.
   * @param bool $edit
   *   Context of this form.
   * @param string $crop_type_id
   *   The id of the current crop.
   *
   * @return array|NULL
   *   Populate all crop elements into the form.
   */
  public static function getCropFormElement(array &$element, $element_wrapper_name, array $original_properties, $edit, $crop_type_id) {
    $crop_properties = self::getCropFormProperties($original_properties, $edit);

    // Generate all coordinate elements into the form when process is active.
    foreach ($crop_properties as $property => $value) {
      $crop_element = &$element['crop_wrapper'][$crop_type_id][$element_wrapper_name]['values'][$property];
      $value_property = self::getCropFormPropertyValue($element, $crop_type_id, $edit, $value['value'], $property);
      $crop_element = [
        '#type' => 'hidden',
        '#attributes' => [
          'class' => ["crop-$property"],
        ],
        '#crop_type' => $crop_type_id,
        '#element_name' => $property,
        '#default_value' => $value_property,
      ];

      if ($property == 'height' || $property == 'width') {
        $crop_element['#element_validate'] = [
          [
            static::class,
            'validateHardLimit',
          ],
        ];
      }
    }
    return $element;
  }

  /**
   * Update crop elements of crop into the form.
   *
   * @param array $original_properties
   *   All properties calculate for apply to.
   * @param bool $edit
   *   Context of this form.
   *
   * @return array<string,array>
   *   Populate all crop elements into the form.
   */
  public static function getCropFormProperties(array $original_properties, $edit) {
    $crop_elements = self::setCoordinatesElement();
    if (!empty($original_properties) && $edit) {
      foreach ($crop_elements as $properties => $value) {
        $crop_elements[$properties]['value'] = $original_properties[$properties];
      }
    }
    return $crop_elements;
  }

  /**
   * Get default value of property elements.
   *
   * @param array $element
   *   All form elements without crop properties.
   * @param string $crop_type
   *   The id of the current crop.
   * @param bool $edit
   *   Context of this form.
   * @param int|NULL $value
   *   The values calculated by ImageCrop::getCropFormProperties().
   * @param string $property
   *   Name of current property @see setCoordinatesElement().
   *
   * @return int|NULL
   *   Value of this element.
   */
  public static function getCropFormPropertyValue(array &$element, $crop_type, $edit, $value, $property) {
    // Standard case.
    if (!empty($edit) && isset($value)) {
      return $value;
    }
    // Populate value when ajax populates values after process.
    if (isset($element['#value']) && isset($element['crop_wrapper'])) {
      $ajax_element = &$element['#value']['crop_wrapper']['container'][$crop_type]['values'];
      return (isset($ajax_element[$property]) && !empty($ajax_element[$property])) ? $ajax_element[$property] : NULL;
    }
    return NULL;
  }

  /**
   * Form element validation handler for crop widget elements.
   *
   * @param array $element
   *   All form elements without crop properties.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @see ImageCrop::getCropFormElement()
   */
  public static function validateHardLimit(array $element, FormStateInterface $form_state) {
    /** @var \Drupal\crop\Entity\CropType $crop_type */
    $crop_type = \Drupal::entityTypeManager()
      ->getStorage('crop_type')
      ->load($element['#crop_type']);
    $parents = $element['#parents'];
    array_pop($parents);
    $crop_values = $form_state->getValue($parents);
    $hard_limit = $crop_type->getHardLimit();
    $action_button = $form_state->getTriggeringElement()['#value'];
    // @todo We need to add this test in multilingual context because,
    // the "#value" element are a simple string in translate form,
    // and an TranslatableMarkup object in other cases.
    $operation = ($action_button instanceof TranslatableMarkup) ? $action_button->getUntranslatedString() : $action_button;

    if ((int) $crop_values['crop_applied'] == 0 || $operation == 'Remove') {
      return;
    }

    $element_name = $element['#element_name'];
    if ($hard_limit[$element_name] !== 0 && !empty($hard_limit[$element_name])) {
      if ($hard_limit[$element_name] > (int) $crop_values[$element_name]) {
        $form_state->setError($element, t('Crop @property is smaller then the allowed @hard_limitpx for @crop_name',
          [
            '@property' => $element_name,
            '@hard_limit' => $hard_limit[$element_name],
            '@crop_name' => $crop_type->label(),
          ]
          ));
      }
    }
  }

  /**
   * Set All sizes properties of the crops.
   *
   * @return array<string,array>
   *   Set all possible crop zone properties.
   */
  public static function setCoordinatesElement() {
    return [
      'x' => ['label' => t('X coordinate'), 'value' => NULL],
      'y' => ['label' => t('Y coordinate'), 'value' => NULL],
      'width' => ['label' => t('Width'), 'value' => NULL],
      'height' => ['label' => t('Height'), 'value' => NULL],
    ];
  }

}
