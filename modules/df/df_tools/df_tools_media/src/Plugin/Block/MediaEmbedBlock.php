<?php

namespace Drupal\df_tools_media\Plugin\Block;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media_entity\Entity\Media;

/**
 * Provides the "Media Embed" block.
 *
 * Copied wholesale from Content Browser.
 * @todo Make a contrib module to provide blocks for every Entity Browser...
 *
 * @Block(
 *   id = "media_embed",
 *   admin_label = @Translation("Media Embed"),
 *   category = @Translation("Embed")
 * )
 */
class MediaEmbedBlock extends BlockBase {

  /**
   * The number of times this block allows rendering the same entity.
   *
   * @var int
   */
  const RECURSIVE_RENDER_LIMIT = 2;

  /**
   * An array of counters for the recursive rendering protection.
   *
   * @var array
   */
  protected static $recursiveRenderDepth = [];

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'view_mode' => '',
      'mids' => [],
      'uuids' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $entities = $form_state->getValue([
      'settings',
      'selection',
      'mids',
      'entities'
    ], []);
    $mids = [];
    foreach ($entities as $entity) {
      $mids[] = $entity->id();
    }
    if (empty($mids)) {
      $mids = $this->getDefaultMIDs();
    }

    $form['selection'] = $this->browserForm($mids);

    /** @var \Drupal\Core\Entity\EntityDisplayRepository $entity_display_repository */
    $entity_display_repository = \Drupal::service('entity_display.repository');
    $view_mode_options = $entity_display_repository->getViewModeOptions('media');

    $form['view_mode'] = [
      '#type' => 'select',
      '#options' => $view_mode_options,
      '#title' => t('View mode'),
      '#default_value' => $this->configuration['view_mode'],
    ];

    return $form;
  }

  /**
   * Constructs parts of the form needed to use Entity Browser.
   *
   * @param array $mids
   *   An array of Media IDs.
   *
   * @return array
   *   A render array representing Entity Browser components.
   */
  public function browserForm($mids) {
    $selection = [
      '#type' => 'container',
      '#attributes' => ['id' => 'media-embed-block-browser'],
    ];

    $selection['mids'] = [
      '#type' => 'entity_browser',
      '#entity_browser' => 'media_browser_in_modal',
      '#entity_browser_validators' => [
        'entity_type' => ['type' => 'media']
      ],
      '#process' => [
        [
          '\Drupal\entity_browser\Element\EntityBrowserElement',
          'processEntityBrowser'
        ],
        [get_called_class(), 'processEntityBrowser'],
      ],
    ];

    $order_class = 'media-embed-block-delta-order';

    $selection['table'] = [
      '#type' => 'table',
      '#header' => [
        t('Title'),
        t('Type'),
        t('Order', [], ['context' => 'Sort order']),
      ],
      '#empty' => t('No media yet'),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => $order_class,
        ],
      ],
    ];

    $delta = 0;
    $bundle_info = \Drupal::service('entity_type.bundle.info')->getBundleInfo('media');

    foreach ($mids as $mid) {
      /** @var \Drupal\media_entity\Entity\Media $media */
      $media = Media::load($mid);

      $selection['table'][$mid] = [
        '#attributes' => [
          'class' => ['draggable'],
          'data-entity-id' => $media->getEntityTypeId() . ':' . $mid,
        ],
        'title' => ['#markup' => $media->label()],
        'type' => ['#markup' => $bundle_info[$media->bundle()]['label']],
        '_weight' => [
          '#type' => 'weight',
          '#title' => t('Weight for row @number', ['@number' => $delta + 1]),
          '#title_display' => 'invisible',
          '#delta' => count($mids),
          '#default_value' => $delta,
          '#attributes' => ['class' => [$order_class]],
        ],
      ];

      $delta++;
    }

    return $selection;
  }

  /**
   * AJAX callback: Re-renders the Entity Browser button/table.
   */
  public static function updateCallback(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $parents = array_slice($trigger['#array_parents'], 0, -2);
    $selection = NestedArray::getValue($form, $parents);
    return $selection;
  }

  /**
   * Render API callback: Processes the entity browser element.
   */
  public static function processEntityBrowser(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['entity_ids']['#ajax'] = [
      'callback' => [get_called_class(), 'updateCallback'],
      'wrapper' => 'media-embed-block-browser',
      'event' => 'entity_browser_value_updated',
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['mids'] = [];
    $this->configuration['uuids'] = [];
    foreach ($form_state->getValue(['selection', 'table'], []) as $mid => $settings) {
      $this->configuration['mids'][] = $mid;
    }
    $this->configuration['view_mode'] = $form_state->getValue('view_mode');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('media');

    foreach ($this->getDefaultMIDs() as $mid) {
      /** @var \Drupal\media_entity\Entity\Media $media */
      $media = Media::load($mid);
      if ($media && $media->access('view')) {
        if (isset(static::$recursiveRenderDepth[$mid])) {
          static::$recursiveRenderDepth[$mid]++;
        }
        else {
          static::$recursiveRenderDepth[$mid] = 1;
        }

        // Protect ourselves from recursive rendering.
        if (static::$recursiveRenderDepth[$mid] > static::RECURSIVE_RENDER_LIMIT) {
          return $build;
        }

        $build[] = $view_builder->view($media, $this->configuration['view_mode']);
      }
    }

    return $build;
  }

  /**
   * Gets the default MIDs for this Block.
   *
   * @return array
   *   An array of Media IDs that are currently set in the Block configuration.
   */
  protected function getDefaultMIDs() {
    // We optionally support UUIDs being put directly to our configuration, to
    // support profiles providing Media Embed Blocks with default config.
    if (!empty($this->configuration['uuids'])) {
      $mids = \Drupal::entityQuery('media')
        ->condition('uuid', $this->configuration['uuids'], 'IN')
        ->execute();
    }
    else {
      $mids = [];
    }

    // Merge in the normal configuration.
    $mids += $this->configuration['mids'];

    return $mids;
  }

}
