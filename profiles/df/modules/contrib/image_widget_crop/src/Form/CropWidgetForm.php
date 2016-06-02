<?php

/**
 * @file
 * Contains \Drupal\image_widget_crop\Form\CropWidgetForm.
 */

namespace Drupal\image_widget_crop\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\crop\Entity\CropType;
use Drupal\image_widget_crop\ImageWidgetCropManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure ImageWidgetCrop general settings for this site.
 */
class CropWidgetForm extends ConfigFormBase {

  /**
   * The settings of image_widget_crop configuration.
   *
   * @var array
   *
   * @see \Drupal\Core\Config\Config
   */
  protected $settings;

  /**
   * Instance of API ImageWidgetCropManager.
   *
   * @var \Drupal\image_widget_crop\ImageWidgetCropManager
   */
  protected $imageWidgetCropManager;

  /**
   * Constructs a CropWidgetForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ImageWidgetCropManager $iwc_manager) {
    parent::__construct($config_factory);
    $this->settings = $this->config('image_widget_crop.settings');
    $this->imageWidgetCropManager = $iwc_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('config.factory'),
      $container->get('image_widget_crop.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'image_widget_crop_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['image_widget_crop.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $url = 'https://cdnjs.com/libraries/cropper';

    $form['library'] = [
      '#type' => 'details',
      '#title' => t('Cropper library settings'),
    ];

    $form['library']['library_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Remote URL for the Cropper library'),
      '#description' => $this->t('Set the URL for a Web-Hosted Cropper library (minified), or leave empty if using the library locally. You can retrieve the library from <a href="@url">Cropper CDN</a>.', [
        '@url' => $url,
      ]),
      '#default_value' => $this->settings->get('settings.library_url'),
    ];

    $form['library']['css_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Remote URL for the Cropper CSS file'),
      '#description' => $this->t('Set the URL for a Web-Hosted Cropper CSS file (minified), or leave empty if using the CSS file locally. You can retrieve the CSS file from <a href="@url">Cropper CDN</a>.', [
        '@url' => $url,
      ]),
      '#default_value' => $this->settings->get('settings.css_url'),
    ];

    $form['image_crop'] = [
      '#type' => 'details',
      '#title' => t('General configuration'),
    ];

    $form['image_crop']['crop_preview_image_style'] = [
      '#title' => $this->t('Crop preview image style'),
      '#type' => 'select',
      '#options' => $this->imageWidgetCropManager->getAvailableCropImageStyle(image_style_options(FALSE)),
      '#default_value' => $this->settings->get('settings.crop_preview_image_style'),
      '#description' => $this->t('The preview image will be shown while editing the content.'),
      '#weight' => 15,
    ];

    $form['image_crop']['crop_list'] = [
      '#title' => $this->t('Crop Type'),
      '#type' => 'select',
      '#options' => $this->imageWidgetCropManager->getAvailableCropType(CropType::getCropTypeNames()),
      '#empty_option' => $this->t('<@no-preview>', ['@no-preview' => $this->t('no preview')]),
      '#default_value' => $this->settings->get('settings.crop_list'),
      '#multiple' => TRUE,
      '#description' => $this->t('The type of crop to apply to your image. If your Crop Type not appear here, set an image style use your Crop Type'),
      '#weight' => 16,
    ];

    $form['image_crop']['show_crop_area'] = [
      '#title' => $this->t('Always expand crop area'),
      '#type' => 'checkbox',
      '#default_value' => $this->settings->get('settings.show_crop_area'),
    ];

    $form['image_crop']['warn_multiple_usages'] = [
      '#title' => $this->t('Warn user when a file have multiple usages'),
      '#type' => 'checkbox',
      '#default_value' => $this->settings->get('settings.warn_multiple_usages'),
    ];

    $form['image_crop']['show_default_crop'] = [
      '#title' => $this->t('Show default crop area'),
      '#type' => 'checkbox',
      '#default_value' => $this->settings->get('settings.show_default_crop'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validation for cropper library.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    // TODO: Change the autogenerated stub.
    if (\Drupal::moduleHandler()->moduleExists('libraries')) {
      $directory = libraries_get_path('cropper') . '/dist/';
      $library = 'cropper.min.js';
      $css = 'cropper.min.css';
      if (!file_exists($directory . $library) || !file_exists($directory . $css)) {
        $form_state->setErrorByName('plugin', t('Either the library file or the CSS file is not present in the directory %directory.', ['%directory' => '/' . $directory]));
      }
    }
    else {
      if (empty($form_state->getValue('library_url')) || empty($form_state->getValue('css_url'))) {
        $form_state->setErrorByName('plugin', t('Either set the library and CSS locally and enable the libraries module or enter the remote URLs below. Check the README.md file for more information.'));
      }
      $cropper_cdn_url = 'https://cdnjs.com/libraries/cropper';
      if (!empty($form_state->getValue('library_url'))) {
        // Check if the name of the library in the remote URL is as expected.
        $library_url = $form_state->getValue('library_url');
        if (parse_url($library_url, PHP_URL_HOST) && parse_url($library_url, PHP_URL_PATH)) {
          $js = pathinfo($library_url, PATHINFO_BASENAME);
          if (!preg_match('/^cropper\.min\.js$/', $js)) {
            $form_state->setErrorByName('plugin', t('The naming of the library is unexpected. Double check that this is the real Cropper library. The URL for the minimized version of the library can be found on <a href="@url">Cropper CDN</a>.', ['@url' => $cropper_cdn_url]), 'warning');
          }
        }
        else {
          $form_state->setErrorByName('plugin', t('The remote URL for the library is unexpected. Please, provide the correct URL to the minimized version of the library found on <a href="@url">Cropper CDN</a>.', ['@url' => $cropper_cdn_url]), 'error');
        }
      }
      elseif (!empty($form_state->getValue('css_url'))) {
        // Check if the name of the library in the remote URL is as expected.
        $css_url = $form_state->getValue('css_url');
        if (parse_url($css_url, PHP_URL_HOST) && parse_url($css_url, PHP_URL_PATH)) {
          $css = pathinfo($css_url, PATHINFO_BASENAME);
          if (!preg_match('/^cropper\.min\.css$/', $css)) {
            $form_state->setErrorByName('plugin', t('The naming of the CSS is unexpected. Double check that this is the real Cropper CSS file. The URL for the minimized version of the CSS fuke can be found on <a href="@url">Cropper CDN</a>.', ['@url' => $cropper_cdn_url]), 'warning');
          }
        }
        else {
          $form_state->setErrorByName('plugin', t('The remote URL for the CSS file is unexpected. Please, provide the correct URL to the minimized version of the CSS file found on <a href="@url">Cropper CDN</a>.', ['@url' => $cropper_cdn_url]), 'error');
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // We need to rebuild the library cache if we switch from remote to local
    // library or vice-versa.
    Cache::invalidateTags(['library_info']);

    $this->settings
      ->set("settings.library_url", $form_state->getValue('library_url'))
      ->set("settings.css_url", $form_state->getValue('css_url'))
      ->set("settings.crop_preview_image_style", $form_state->getValue('crop_preview_image_style'))
      ->set("settings.show_default_crop", $form_state->getValue('show_default_crop'))
      ->set("settings.show_crop_area", $form_state->getValue('show_crop_area'))
      ->set("settings.warn_multiple_usages", $form_state->getValue('warn_multiple_usages'))
      ->set("settings.crop_list", $form_state->getValue('crop_list'));
    $this->settings->save();
  }

}
