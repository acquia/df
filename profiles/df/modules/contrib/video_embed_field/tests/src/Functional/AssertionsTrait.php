<?php

namespace Drupal\Tests\video_embed_field\Functional;

/**
 * Some legacy assertions that existed in simpletest.
 */
trait AssertionsTrait {

  /**
   * Make an assertion on the page HTML.
   *
   * @param string $content
   *   The content to assert on the current page.
   */
  protected function assertRaw($content) {
    $this->assertTrue(strpos($this->getSession()->getPage()->getHtml(), (string) $content) !== FALSE);
  }

  /**
   * Make an assertion on the page HTML.
   *
   * @param string $content
   *   The content to assert is not on the current page.
   */
  protected function assertNoRaw($content) {
    $this->assertTrue(strpos($this->getSession()->getPage()->getHtml(), (string) $content) === FALSE);
  }

  /**
   * Make an assertion on the page text.
   *
   * @param string $text
   *   The text to assert on the current page.
   */
  protected function assertText($text) {
    $this->assertTrue(strpos(strip_tags($this->getSession()->getPage()->getContent()), (string) $text) !== FALSE);
  }

  /**
   * Assert a field value by xpath query.
   *
   * @param string $xpath
   *   The xpath query.
   * @param string $value
   *   The field value.
   */
  protected function assertFieldByXpath($xpath, $value) {
    $this->assertEquals($value, $this->getSession()->getPage()->find('xpath', $xpath)->getValue());
  }

}
