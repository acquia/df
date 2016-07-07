<?php

namespace Drupal\df_tools_frontend\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\Element\StatusMessages;
use Drupal\file\Entity\File;
use Drupal\quickedit_image\Controller\QuickeditImageController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Returns responses for custom Quickedit Image module routes.
 */
class QuickeditImageBrowserController extends QuickeditImageController {

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
    $fid = $request->request->get('fid');
    if (!$fid || !is_numeric($fid)) {
      throw new BadRequestHttpException('FID missing or invalid.');
    }

    $field = $this->getField($entity, $field_name, $langcode);
    $field_validators = $field->getUploadValidators();
    $field_settings = $field->getFieldDefinition()->getSettings();

    // Add upload resolution validation.
    if ($field_settings['max_resolution'] || $field_settings['min_resolution']) {
      $field_validators['file_validate_image_resolution'] = [$field_settings['max_resolution'], $field_settings['min_resolution']];
    }

    // Attempt to load the image given the field's constraints.
    /** @var \Drupal\file\Entity\file $file */
    $file = File::load($request->request->get('fid'));
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

      // Set the value in the Entity to the new file.
      $this->saveEntity($entity, $field_name, $file);

      // Render the new image using the correct formatter settings.
      $output = $this->buildImage($entity, $field_name, $view_mode_id, $langcode);

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
