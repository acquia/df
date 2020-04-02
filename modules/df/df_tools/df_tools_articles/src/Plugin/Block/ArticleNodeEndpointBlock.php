<?php

namespace Drupal\df_tools_articles\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\Client;

/**
 * Provides a 'ArticleNodeEndpointBlock' block.
 *
 * @Block(
 *  id = "article_node_endpoint_block",
 *  admin_label = @Translation("Article node endpoint block"),
 *  category = "Lists",
 * )
 */
class ArticleNodeEndpointBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * GuzzleHttp\Client definition.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * CollectionNodeEndpointBlock constructor.
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \GuzzleHttp\Client $http_client
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array();
    global $base_url;
    header('Content-Type: application/json');
    $json_output = (string) $this->httpClient->get($base_url . '/api/node/article')->getBody();
    $json_pretty = json_encode(json_decode($json_output), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $json_indented_by_2 = preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $json_pretty);
    $build['article_node_endpoint_block']['#markup'] = '<a class="btn btn-primary" data-toggle="modal" data-target="#apiModal" href="#open" role="button" aria-expanded="false" aria-controls="collapseExample">Expand API Response</a><div class="apiResponse"><pre><code class="language-json">' . $json_indented_by_2 . '</code></pre></div><div class="modal" id="apiModal"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h1 class="modal-title">API Response</h1><a type="button" class="close" data-dismiss="modal" href="#close">×</a></div><pre><code class="language-json">' . $json_indented_by_2 . '</code></pre><div class="modal-footer"><a type="button" class="btn btn-danger" data-dismiss="modal" href="#close">Close</a></div></div></div></div>';
    $build['article_node_endpoint_block']['#attached']['library'][] = 'df_tools_articles/main';
    return $build;
  }

}
