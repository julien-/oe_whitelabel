<?php

declare(strict_types=1);

namespace Drupal\Tests\oe_whitelabel\Functional;

/**
 * Tests that the language change link has the correct aria-label.
 */
class LanguageSwitcherAccessibleTest extends WhitelabelBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'oe_bootstrap_theme_helper',
    'oe_multilingual',
    'oe_whitelabel_multilingual',
  ];

  /**
   * Tests the aria-label for the language change link.
   */
  public function testLanguageChangeAriaLabel(): void {
    $this->drupalGet('<front>');
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();

    $assert_session->elementExists('css', '.language-switcher a');

    $link = $page->find('css', '.language-switcher a');
    $aria_label_en = $link->getAttribute('aria-label');
    $this->assertEquals('Change language. Current language is English.', $aria_label_en);
    $link->click();

    // Wait for the modal to be visible (if necessary).
    $this->assertSession()->elementExists('css', '.bcl-language-list-modal');

    $modal_page = $this->getSession()->getPage();
    $dutch_link = $modal_page->find('css', 'a#link_nl');

    $dutch_link->click();

    $link = $this->getSession()->getPage()->find('css', '.language-switcher a');
    $aria_label_nl = $link->getAttribute('aria-label');
    $this->assertEquals('Change language. Current language is Nederlands.', $aria_label_nl);
  }

}
