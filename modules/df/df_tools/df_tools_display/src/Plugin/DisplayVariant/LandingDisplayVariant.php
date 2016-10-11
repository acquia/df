<?php

/**
 * @file
 * Contains \Drupal\df_tools_display\Plugin\DisplayVariant\LandingDisplayVariant.
 */

namespace Drupal\df_tools_display\Plugin\DisplayVariant;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\page_manager\Plugin\DisplayVariant\PageBlockDisplayVariant;

/**
 * Provides a display variant that simply contains blocks.
 *
 * @DisplayVariant(
 *   id = "landing_display",
 *   admin_label = @Translation("Landing page")
 * )
 */
class LandingDisplayVariant extends PageBlockDisplayVariant {

  /**
   * @see \Drupal\page_manager\Plugin\PageBlockVariantInterface::getRegionNames()
   */
  public function getRegionNames() {
    return [
      'top' => 'Top',
      'primary' => 'Primary',
      'secondary' => 'Secondary',
      'middle' => 'Middle',
      'sidebar' => 'Sidebar',
      'bottom' => 'Bottom',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildRegions(array $build) {
    // Get page_class configuration for our display variant.
    $class = Html::getClass($this->configuration['page_class']);
    if ($class == '') {
      $class = 'default';
    }

    // Append the prefix before buildRegions.
    $build['landing-top']['#prefix'] = '<div class="landing-display-' . $class . '">';

    // Build the regions via PageBlockDisplayVariant::buildRegions.
    $build = parent::buildRegions($build);

    // Add suffix for wrapper after buildRegions.
    $build['landing-bottom']['#suffix'] = '</div>';

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    // Allow to configure the variant class, even when adding a new display.
    $form['page_class'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Page class'),
        '#description' => $this->t('Configure the wrapper class that will be used for this display.'),
        '#default_value' => $this->configuration['page_class'] ?: '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if ($form_state->hasValue('page_class')) {
      $this->configuration['page_class'] = $form_state->getValue('page_class');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
        'page_class' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function renderPageTitle($page_title) {
    $markup = parent::renderPageTitle($page_title);
    return $this->t((string) $markup);
  }

}
