<?php

namespace Drupal\Tests\node_display_title\Functional;

use Drupal\Tests\node\Functional\NodeTestBase;

/**
 * Class NodeDisplayTitleTest. The base class for testing node display title.
 */
class NodeDisplayTitleTest extends NodeTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node', 'node_display_title'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Test node display title.
   */
  public function testNodeDisplayTitle() {
    // Log in as an admin user with permission to manage node display settings.
    $admin = $this->drupalCreateUser([], NULL, TRUE);
    $this->drupalLogin($admin);

    // Create a new node type.
    $this->createContentType(['type' => 'display']);

    // Enable private fields for node content type.
    $config = \Drupal::configFactory()->getEditable('node_display_title.settings');
    $config->set('bundles', ['display' => 'display']);
    $config->save();

    // Check the status of the page.
    $this->drupalGet('node/add/display');
    $this->assertSession()->statusCodeEquals(200);

    // Create a new node with a random name.
    $edit = [];
    $edit['title[0][value]'] = $this->randomMachineName(8);
    $edit['display_title[0][value]'] = $this->randomMachineName(8);
    $this->drupalGet('node/add/display');
    $this->submitForm($edit, 'Save');

    // Get created node.
    $node = $this->drupalGetNodeByTitle($edit['title[0][value]']);

    // Check if the display title exists on the node overview.
    $this->drupalGet("node/{$node->id()}");
    $this->assertSession()->pageTextContains($edit['display_title[0][value]']);
    $this->assertSession()->pageTextNotContains($edit['title[0][value]']);

    // Check if the display title doesn't exist on the content page.
    $this->drupalGet('admin/content');
    $this->assertSession()->pageTextContains($edit['title[0][value]']);
    $this->assertSession()->pageTextNotContains($edit['display_title[0][value]']);
  }

}
