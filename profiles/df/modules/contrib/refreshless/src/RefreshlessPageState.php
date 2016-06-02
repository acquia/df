<?php

/**
 * @file
 * Contains \Drupal\refreshless\RefreshlessPageState.
 */

namespace Drupal\refreshless;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextsManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RefreshlessPageState {

  /**
   * The cache contexts manager.
   *
   * @var \Drupal\Core\Cache\Context\CacheContextsManager
   */
  protected $cacheContextsManager;

  /**
   * The CSRF token generator.
   *
   * @var \Drupal\Core\Access\CsrfTokenGenerator
   */
  protected $csrfTokenGenerator;

  /**
   * Constructs a new RefreshlessPageState instance.
   *
   * @param \Drupal\Core\Cache\Context\CacheContextsManager $cache_contexts_manager
   *   The cache contexts manager.
   * @param \Drupal\Core\Access\CsrfTokenGenerator $csrf_token_generator
   *   The CSRF token generator.
   */
  public function __construct(CacheContextsManager $cache_contexts_manager, CsrfTokenGenerator $csrf_token_generator) {
    $this->cacheContextsManager = $cache_contexts_manager;
    $this->csrfToken = $csrf_token_generator;
  }

  /**
   * Builds the Refreshless page state for the given response cacheability.
   *
   * @param \Drupal\Core\Cache\CacheableMetadata $cacheability
   *   Response cacheability metadata (or at least the cacheability metadata of
   *   all rendered parts of the page that are eligible for Refreshless-based
   *   updating).
   *
   * @return array
   *   The Refreshless page state to be used in #attached[drupalSettings].
   */
  public function build(CacheableMetadata $cacheability) {
    // For now, the Refreshless page state only consists of the context hashes.
    return $this->getSensitiveContextHashes($cacheability->getCacheContexts());
  }

  /**
   * Reads the Refreshless page state from the current request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   The Refreshless page state.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   When the Refreshless page state is missing.
   */
  protected function read(Request $request) {
    if (!$request->query->get('refreshless_page_state')) {
      throw new HttpException('Refreshless page state is missing.');
    }

    $refreshless_page_state = $request->query->get('refreshless_page_state');
    return [
      // @see build()
      'cache_contexts' => $refreshless_page_state,
    ];
  }

  /**
   * Check whether a region (or block or …) has changed from the previous page.
   *
   * @param \Drupal\Core\Cache\CacheableMetadata $cacheability
   *   The cacheability of a region (or block or …), to check whether it would
   *   have changed compared to the previous page.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request, to check whether
   *
   * @return bool
   *   Whether this region (or block or …) has changed.
   */
  public function hasChanged(CacheableMetadata $cacheability, Request $request) {
    $current_context_hashes = $this->getSensitiveContextHashes($cacheability->getCacheContexts());
    $previous_context_hashes = $this->read($request)['cache_contexts'];

    return $current_context_hashes != array_intersect_key($previous_context_hashes, $current_context_hashes);
  }

  /**
   * Indicates whether Refreshless is sensitive to changes in this cache context.
   *
   * Refreshless is sensitive to only the 'url' (and 'url.*') cache context and
   * the 'route' (and 'route.*') cache context.
   *
   * @todo can be simplified to just 'url' (and 'url.*') once https://www.drupal.org/node/2453835 lands.
   *
   * @param string $context_token
   *   The cache context token to check.
   *
   * @return bool
   *   Whether the given cache context token is
   */
  protected function isSensitiveContext($context_token) {
    assert('is_string($context_token)');
    return strpos($context_token, 'url') === 0 || strpos($context_token, 'route') === 0;
  }

  /**
   * Gets the context hashes that Refreshless is sensitive to.
   *
   * @param string[] $context_tokens
   *   A set of cache context tokens.
   *
   * @return string[]
   *   The context hashes of sensitive context tokens, keyed by the context
   *   tokens.
   */
  public function getSensitiveContextHashes(array $context_tokens) {
    $context_hashes = [];
    foreach ($context_tokens as $context_token) {
      if ($this->isSensitiveContext($context_token)) {
        $context_value = $this->cacheContextsManager->convertTokensToKeys([$context_token])->getKeys()[0];
        $context_hashes[$context_token] = $this->csrfToken->get($context_value);
      }
    }
    return $context_hashes;
  }

}
