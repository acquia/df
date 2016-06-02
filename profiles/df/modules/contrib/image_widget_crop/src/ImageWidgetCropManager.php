<?php

/**
 * @file
 * Contains of \Drupal\image_widget_crop\ImageWidgetCropManager.
 */

namespace Drupal\image_widget_crop;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Image\Image;
use Drupal\crop\Entity\Crop;
use Drupal\crop\Entity\CropType;
use Drupal\image\Entity\ImageStyle;

/**
 * ImageWidgetCropManager calculation class.
 */
class ImageWidgetCropManager {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface;
   */
  protected $entityTypeManager;

  /**
   * The crop storage.
   *
   * @var \Drupal\crop\CropStorage.
   */
  protected $cropStorage;

  /**
   * The image style storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorage.
   */
  protected $imageStyleStorage;

  /**
   * The File storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorage.
   */
  protected $fileStorage;

  /**
   * Constructs a ImageWidgetCropManager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->cropStorage = $this->entityTypeManager->getStorage('crop');
    $this->imageStyleStorage = $this->entityTypeManager->getStorage('image_style');
    $this->fileStorage = $this->entityTypeManager->getStorage('file');
  }

  /**
   * Create new crop entity with user properties.
   *
   * @param array $properties
   *   All properties returned by the crop plugin (js),
   *   and the size of thumbnail image.
   * @param array|mixed $field_value
   *   An array of values for the contained properties of image_crop widget.
   * @param CropType $crop_type
   *   The entity CropType.
   */
  public function applyCrop(array $properties, $field_value, CropType $crop_type) {
    // Get Original sizes and position of crop zone.
    $crop_properties = $this->getCropOriginalDimension($field_value, $properties);
    // Get all imagesStyle used this crop_type.
    $image_styles = $this->getImageStylesByCrop($crop_type->id());

    $this->saveCrop($crop_properties, $field_value, $image_styles, $crop_type, FALSE);
  }

  /**
   * Update old crop with new properties choose in UI.
   *
   * @param array $properties
   *   All properties returned by the crop plugin (js),
   *   and the size of thumbnail image.
   * @param array|mixed $field_value
   *   An array of values contain properties of image_crop widget.
   * @param CropType $crop_type
   *   The entity CropType.
   */
  public function updateCrop(array $properties, $field_value, CropType $crop_type) {
    // Get Original sizes and position of crop zone.
    $crop_properties = $this->getCropOriginalDimension($field_value, $properties);

    // Get all imagesStyle used this crop_type.
    $image_styles = $this->getImageStylesByCrop($crop_type->id());

    if (!empty($image_styles)) {
      $crops = $this->loadImageStyleByCrop($image_styles, $crop_type, $field_value['file-uri']);
    }

    // If any crop exist add new crop.
    if (empty($crops)) {
      $this->saveCrop($crop_properties, $field_value, $image_styles, $crop_type);
      return;
    }

    foreach ($crops as $crop_element) {
      // Get Only first crop entity @see https://www.drupal.org/node/2617818.
      /** @var \Drupal\crop\Entity\Crop $crop */
      $crop = $crop_element;

      if (!$this->cropHasChanged($crop_properties, array_merge($crop->position(), $crop->size()))) {
        return;
      }

      $this->updateCropProperties($crop, $crop_properties);
      $this->imageStylesOperations($image_styles, $field_value['file-uri']);
      drupal_set_message(t('The crop "@cropType" were successfully updated for image "@filename".', ['@cropType' => $crop_type->label(), '@filename' => $this->fileStorage->load($field_value['file-id'])->getFilename()]));
    }
  }

  /**
   * Save the crop when this crop not exist.
   *
   * @param double[] $crop_properties
   *   The properties of the crop applied to the original image (dimensions).
   * @param array|mixed $field_value
   *   An array of values for the contained properties of image_crop widget.
   * @param array $image_styles
   *   The list of imagesStyle available for this crop.
   * @param CropType $crop_type
   *   The entity CropType.
   * @param bool $notify
   *   Show notification after actions (default TRUE).
   */
  public function saveCrop(array $crop_properties, $field_value, array $image_styles, CropType $crop_type, $notify = TRUE) {
    $values = [
      'type' => $crop_type->id(),
      'entity_id' => $field_value['file-id'],
      'entity_type' => 'file',
      'uri' => $field_value['file-uri'],
      'x' => $crop_properties['x'],
      'y' => $crop_properties['y'],
      'width' => $crop_properties['width'],
      'height' => $crop_properties['height'],
    ];

    // Save crop with previous values.
    /** @var \Drupal\crop\CropInterface $crop */
    $crop = $this->cropStorage->create($values);
    $crop->save();

    $this->imageStylesOperations($image_styles, $field_value['file-uri'], TRUE);

    if ($notify) {
      drupal_set_message(t('The crop "@cropType" was successfully added for image "@filename".', ['@cropType' => $crop_type->label(), '@filename' => $this->fileStorage->load($field_value['file-id'])->getFilename()]));
    }
  }

  /**
   * Delete the crop when user delete it.
   *
   * @param string $file_uri
   *   Uri of image uploaded by user.
   * @param \Drupal\crop\Entity\CropType $crop_type
   *   The CropType object.
   * @param int $file_id
   *   Id of image uploaded by user.
   */
  public function deleteCrop($file_uri, CropType $crop_type, $file_id) {
    $image_styles = $this->getImageStylesByCrop($crop_type->id());
    $crop = $this->cropStorage->loadByProperties([
      'type' => $crop_type->id(),
      'uri' => $file_uri
    ]);
    $this->cropStorage->delete($crop);
    $this->imageStylesOperations($image_styles, $file_uri);
    drupal_set_message(t('The crop "@cropType" was successfully deleted for image "@filename".', [
      '@cropType' => $crop_type->label(),
      '@filename' => $this->fileStorage->load($file_id)->getFilename()
    ]));
  }

  /**
   * Get center of crop selection.
   *
   * @param int[] $axis
   *   Coordinates of x-axis & y-axis.
   * @param array $crop_selection
   *   Coordinates of crop selection (width & height).
   *
   * @return array<string,double>
   *   Coordinates (x-axis & y-axis) of crop selection zone.
   */
  public function getAxisCoordinates(array $axis, array $crop_selection) {
    return [
      'x' => (int) round($axis['x'] + ($crop_selection['width'] / 2)),
      'y' => (int) round($axis['y'] + ($crop_selection['height'] / 2)),
    ];
  }

  /**
   * Get the size and position of the crop.
   *
   * @param array $field_values
   *   The original values of image.
   * @param array $properties
   *   The original height of image.
   *
   * @return NULL|array
   *   The data dimensions (width & height) into this ImageStyle.
   */
  public function getCropOriginalDimension(array $field_values, array $properties) {
    $crop_coordinates = [];

    /** @var \Drupal\Core\Image\Image $image */
    $image = \Drupal::service('image.factory')->get($field_values['file-uri']);
    if (!$image->isValid()) {
      drupal_set_message(t('The file "@file" is not valid, your crop is not applied.', [
        '@file' => $field_values['file-uri'],
      ]), 'error');
      return NULL;
    }

    // Get Center coordinate of crop zone on original image.
    $axis_coordinate = $this->getAxisCoordinates(
      ['x' => $properties['x'], 'y' => $properties['y']],
      ['width' => $properties['width'], 'height' => $properties['height']]
    );

    // Calculate coordinates (position & sizes) of crop zone on original image.
    $crop_coordinates['width'] = $properties['width'];
    $crop_coordinates['height'] = $properties['height'];
    $crop_coordinates['x'] = $axis_coordinate['x'];
    $crop_coordinates['y'] = $axis_coordinate['y'];

    return $crop_coordinates;
  }

  /**
   * Get one effect instead of ImageStyle.
   *
   * @param \Drupal\image\Entity\ImageStyle $image_style
   *   The ImageStyle to get data.
   * @param string $data_type
   *   The type of data needed in current ImageStyle.
   *
   * @return mixed|NULL
   *   The effect data in current ImageStyle.
   */
  public function getEffectData(ImageStyle $image_style, $data_type) {
    $data = NULL;
    /* @var  \Drupal\image\ImageEffectInterface $effect */
    foreach ($image_style->getEffects() as $uuid => $effect) {
      $data_effect = $image_style->getEffect($uuid)->getConfiguration()['data'];
      if (isset($data_effect[$data_type])) {
        $data = $data_effect[$data_type];
      }
    }

    return $data;
  }

  /**
   * Get the imageStyle using this crop_type.
   *
   * @param string $crop_type_name
   *   The id of the current crop_type entity.
   *
   * @return array
   *   All imageStyle used by this crop_type.
   */
  public function getImageStylesByCrop($crop_type_name) {
    $styles = [];
    $image_styles = $this->imageStyleStorage->loadMultiple();

    /** @var \Drupal\image\Entity\ImageStyle $image_style */
    foreach ($image_styles as $image_style) {
      $image_style_data = $this->getEffectData($image_style, 'crop_type');
      if (!empty($image_style_data) && ($image_style_data == $crop_type_name)) {
        $styles[] = $image_style;
      }
    }

    return $styles;
  }

  /**
   * Apply different operation on ImageStyles.
   *
   * @param array $image_styles
   *   All ImageStyles used by this cropType.
   * @param string $file_uri
   *   Uri of image uploaded by user.
   * @param bool $create_derivative
   *   Boolean to create an derivative of the image uploaded.
   */
  public function imageStylesOperations(array $image_styles, $file_uri, $create_derivative = FALSE) {
    /** @var \Drupal\image\Entity\ImageStyle $image_style */
    foreach ($image_styles as $image_style) {
      if ($create_derivative) {
        // Generate the image derivative uri.
        $destination_uri = $image_style->buildUri($file_uri);

        // Create a derivative of the original image with a good uri.
        $image_style->createDerivative($file_uri, $destination_uri);
      }
      // Flush the cache of this ImageStyle.
      $image_style->flush($file_uri);
    }
  }

  /**
   * Update existent crop entity properties.
   *
   * @param \Drupal\crop\Entity\Crop $crop
   *   The crop object loaded.
   * @param array $crop_properties
   *   The machine name of ImageStyle.
   */
  public function updateCropProperties(Crop $crop, array $crop_properties) {
    // Parse all properties if this crop have changed.
    foreach ($crop_properties as $crop_coordinate => $value) {
      // Edit the crop properties if he have changed.
      $crop->set($crop_coordinate, $value, TRUE)
        ->save();
    }
  }

  /**
   * Load all crop using the ImageStyles.
   *
   * @param array $image_styles
   *   All ImageStyle for this current CROP.
   * @param CropType $crop_type
   *   The entity CropType.
   * @param string $file_uri
   *   Uri of uploded file.
   *
   * @return array
   *   All crop used this ImageStyle.
   */
  public function loadImageStyleByCrop(array $image_styles, CropType $crop_type, $file_uri) {
    $crops = [];
    /** @var \Drupal\image\Entity\ImageStyle $image_style */
    foreach ($image_styles as $image_style) {
      /** @var \Drupal\crop\Entity\Crop $crop */
      $crop = Crop::findCrop($file_uri, $crop_type->id());
      if (!empty($crop)) {
        $crops[$image_style->id()] = $crop;
      }
    }

    return $crops;
  }

  /**
   * Compare crop zone properties when user saved one crop.
   *
   * @param array $crop_properties
   *   The crop properties after saved the form.
   * @param array $old_crop
   *   The crop properties save in this crop entity,
   *   Only if this crop already exist.
   *
   * @return bool
   *   Return true if properties is not identical.
   */
  public function cropHasChanged(array $crop_properties, array $old_crop) {
    if (!empty(array_diff_assoc($crop_properties, $old_crop))) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Verify if ImageStyle is correctly configured.
   *
   * @param array $styles
   *   The list of available ImageStyle.
   *
   * @return array<integer>
   *   The list of styles filtred.
   */
  public function getAvailableCropImageStyle(array $styles) {
    $available_styles = [];
    foreach ($styles as $style_id => $style_label) {
      $style_loaded = $this->imageStyleStorage->loadByProperties(['name' => $style_id]);
      /** @var \Drupal\image\Entity\ImageStyle $image_style */
      $image_style = $style_loaded[$style_id];
      $effect_data = $this->getEffectData($image_style, 'width');
      if (!empty($effect_data)) {
        $available_styles[$style_id] = $style_label;
      }
    }

    return $available_styles;
  }

  /**
   * Verify if the crop is used by a ImageStyle.
   *
   * @param array $crop_list
   *   The list of existent Crop Type.
   *
   * @return array<integer>
   *   The list of Crop Type filtred.
   */
  public function getAvailableCropType(array $crop_list) {
    $available_crop = [];
    foreach ($crop_list as $crop_machine_name => $crop_label) {
      $image_styles = $this->getImageStylesByCrop($crop_machine_name);
      if (!empty($image_styles)) {
        $available_crop[$crop_machine_name] = $crop_label;
      }
    }

    return $available_crop;
  }

  /**
   * Get All sizes properties of the crops for an file.
   *
   * @param \Drupal\crop\Entity\Crop $crop
   *   All crops attached to this file based on URI.
   *
   * @return array<array>
   *   Get all crop zone properties (x, y, height, width),
   */
  public static function getCropProperties(Crop $crop) {
    $anchor = $crop->anchor();
    $size = $crop->size();
    return [
      'x' => $anchor['x'],
      'y' => $anchor['y'],
      'height' => $size['height'],
      'width' => $size['width']
    ];
  }

}
