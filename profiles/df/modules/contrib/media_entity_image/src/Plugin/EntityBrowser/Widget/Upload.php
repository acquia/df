<?php

namespace Drupal\media_entity_image\Plugin\EntityBrowser\Widget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\entity_browser\Plugin\EntityBrowser\Widget\Upload as FileUpload;

/**
 * Uses upload to create media entity images.
 *
 * @EntityBrowserWidget(
 *   id = "media_entity_image_upload",
 *   label = @Translation("Upload images"),
 *   description = @Translation("Upload widget that creates media entity images.")
 * )
 */
class Upload extends FileUpload {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'extensions' => 'jpg jpeg png gif',
      'media_bundle' => NULL,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function getForm(array &$original_form, FormStateInterface $form_state, array $aditional_widget_parameters) {
    /** @var \Drupal\media_entity\MediaBundleInterface $bundle */
    if (!$this->configuration['media_bundle'] || !($bundle = $this->entityManager->getStorage('media_bundle')->load($this->configuration['media_bundle']))) {
      return ['#markup' => $this->t('The media bundle is not configured correctly.')];
    }

    if ($bundle->getType()->getPluginId() != 'image') {
      return ['#markup' => $this->t('The configured bundle is not using image plugin.')];
    }

    $form = parent::getForm($original_form, $form_state, $aditional_widget_parameters);
    $form['upload']['#upload_validators']['file_validate_extensions'] = [$this->configuration['extensions']];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array &$element, array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\media_entity\MediaBundleInterface $bundle */
    $bundle = $this->entityManager
      ->getStorage('media_bundle')
      ->load($this->configuration['media_bundle']);
    $files = $this->extractFiles($form_state);

    $images = [];
    foreach ($files as $file) {
      /** @var \Drupal\media_entity\MediaInterface $image */
      $image = $this->entityManager->getStorage('media')->create([
        'bundle' => $bundle->id(),
        $bundle->getTypeConfiguration()['source_field'] => $file,
      ]);
      $image->save();
      $images[] = $image;
    }

    $this->selectEntities($images, $form_state);
    $this->clearFormValues($element, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['extensions'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed extensions'),
      '#default_value' => $this->configuration['extensions'],
      '#required' => TRUE,
    ];

    $bundle_options = [];
    $bundles = $this
      ->entityManager
      ->getStorage('media_bundle')
      ->loadByProperties(['type' => 'image']);

    foreach ($bundles as $bundle) {
      $bundle_options[$bundle->id()] = $bundle->label();
    }

    if (empty($bundle_options)) {
      $url = Url::fromRoute('media.bundle_add')->toString();
      $form['media_bundle'] = [
        '#markup' => $this->t("You don't have media bundle of the Image type. You should <a href='!link'>create one</a>", ['!link' => $url]),
      ];
    }
    else {
      $form['media_bundle'] = [
        '#type' => 'select',
        '#title' => $this->t('Media bundle'),
        '#default_value' => $this->configuration['media_bundle'],
        '#options' => $bundle_options,
      ];
    }

    return $form;
  }

}
