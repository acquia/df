<?php

namespace Drupal\df_tools_frontend\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\Element\StatusMessages;
use Drupal\image\Controller\QuickEditImageController;
use Drupal\media_entity\Entity\Media;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Returns responses for custom Quickedit Image module routes.
 */
class QuickEditImageBrowserController extends QuickEditImageController {

  /**
   * Returns a JSON object representing the existing file, or validation
   * errors.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity of which an image field is being rendered.
   * @param string $field_name
   *   The name of the (image) field that is being rendered
   * @param string $langcode
   *   The language code of the field that is being rendered.
   * @param string $view_mode_id
   *   The view mode of the field that is being rendered.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The Ajax response.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   *   Throws an exception if the request is invalid.
   */
  public function existing(EntityInterface $entity, $field_name, $langcode, $view_mode_id, Request $request) {
    $mid = $request->request->get('mid');
    if (!$mid || !is_numeric($mid)) {
      throw new BadRequestHttpException('MID missing or invalid.');
    }

    $field = $this->getField($entity, $field_name, $langcode);
    $field_validators = $field->getUploadValidators();
    $field_settings = $field->getFieldDefinition()->getSettings();

    // Add upload resolution validation.
    if ($field_settings['max_resolution'] || $field_settings['min_resolution']) {
      $field_validators['file_validate_image_resolution'] = [$field_settings['max_resolution'], $field_settings['min_resolution']];
    }

    // Attempt to load the image given the field's constraints.
    $media = Media::load($mid);
    if (!$media || !$media->bundle() == 'image') {
      throw new BadRequestHttpException('Media Entity not found or not of bundle "Image".');
    }

    /** @var \Drupal\file\Entity\file $file */
    $file = $media->get('image')->first()->get('entity')->getTarget()->getValue();
    if ($file) {
      // Call the validation functions specified by this function's caller.
      $errors = file_validate($file, $field_validators);

      // Check for errors.
      if (!empty($errors)) {
        $message = array(
          'error' => array(
            '#markup' => $this->t('The specified file %name could not be uploaded.', array('%name' => $file->getFilename())),
          ),
          'item_list' => array(
            '#theme' => 'item_list',
            '#items' => $errors,
          ),
        );
        // Return a JSON object containing the errors from Drupal and our
        // "main_error", which is displayed inside the dropzone area.
        drupal_set_message(\Drupal::service('renderer')->renderPlain($message), 'error');
        $messages = StatusMessages::renderMessages('error');
        return new JsonResponse(['errors' => $this->renderer->render($messages), 'main_error' => $this->t('The requested image failed validation.')]);
      }

      $image = $this->imageFactory->get($file->getFileUri());

      // Set the value in the Entity to the new file.
      /** @var \Drupal\file\Plugin\Field\FieldType\FileFieldItemList $field_list */
      $value = $entity->$field_name->getValue();
      $value[0]['target_id'] = $file->id();
      $value[0]['width'] = $image->getWidth();
      $value[0]['height'] = $image->getHeight();
      $entity->$field_name->setValue($value);

      // Render the new image using the correct formatter settings.
      $entity_view_mode_ids = array_keys($this->entityManager()->getViewModes($entity->getEntityTypeId()));
      if (in_array($view_mode_id, $entity_view_mode_ids)) {
        $output = $entity->$field_name->view($view_mode_id);
      }
      else {
        // Each part of a custom (non-Entity Display) view mode ID is separated
        // by a dash; the first part must be the module name.
        $mode_id_parts = explode('-', $view_mode_id, 2);
        $module = reset($mode_id_parts);
        $args = [$entity, $field_name, $view_mode_id, $langcode];
        $output = $this->moduleHandler()->invoke($module, 'quickedit_render_field', $args);
      }

      // Save the Entity to tempstore.
      $this->tempStore->set($entity->uuid(), $entity);

      $data = [
        'fid' => $file->id(),
        'html' => $this->renderer->renderRoot($output),
      ];
      return new JsonResponse($data);
    }
    else {
      return new JsonResponse(['main_error' => $this->t('File does not exist.')]);
    }
  }

}
