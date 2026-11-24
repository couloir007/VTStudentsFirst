<?php

declare(strict_types=1);

namespace Drupal\Tests\ck5_block_embed\Unit\Plugin\Ck5BlockEmbed;

use Drupal\Tests\UnitTestCase;
use Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed\ViewBlock;

/**
 * Unit tests for the ViewBlock plugin.
 *
 * Cases requiring Views::getView() (a static service-wrapper call) cannot be
 * fully unit-tested and are covered by separate kernel tests. This suite
 * tests every code-path that does not need a running Views installation.
 *
 * @group ck5_block_embed
 * @coversDefaultClass \Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed\ViewBlock
 */
class ViewBlockTest extends UnitTestCase {

  /**
   * Creates a ViewBlock plugin instance for testing.
   *
   * @param array $configuration
   *   Optional plugin configuration.
   *
   * @return \Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed\ViewBlock
   *   The plugin instance.
   */
  protected function createPlugin(array $configuration = []): ViewBlock {
    $plugin = new ViewBlock(
      $configuration,
      'view_block',
      ['id' => 'view_block', 'label' => 'View Block', 'description' => ''],
    );
    $plugin->setStringTranslation($this->getStringTranslationStub());
    return $plugin;
  }

  /**
   * @covers ::defaultConfiguration
   */
  public function testDefaultConfiguration(): void {
    $this->assertSame(['block_id' => NULL], $this->createPlugin()->defaultConfiguration());
  }

  /**
   * @covers ::isInline
   */
  public function testIsInline(): void {
    $this->assertFalse($this->createPlugin()->isInline());
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsFallbackWhenNoBlockId(): void {
    $build = $this->createPlugin()->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('No content block selected', (string) $build['#markup']);
    $this->assertSame(0, $build['#cache']['max-age']);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsErrorOnInvalidJson(): void {
    // json_decode() of a plain string returns NULL, triggering the guard.
    $build = $this->createPlugin(['block_id' => 'not-valid-json'])->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('Invalid view block configuration', (string) $build['#markup']);
    $this->assertSame(0, $build['#cache']['max-age']);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsErrorWhenViewIdMissing(): void {
    // Valid JSON but missing the view_id key.
    $block_id = json_encode(['display_id' => 'block_1']);
    $build = $this->createPlugin(['block_id' => $block_id])->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('Invalid view block configuration', (string) $build['#markup']);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsErrorWhenDisplayIdMissing(): void {
    // Valid JSON but missing the display_id key.
    $block_id = json_encode(['view_id' => 'frontpage']);
    $build = $this->createPlugin(['block_id' => $block_id])->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('Invalid view block configuration', (string) $build['#markup']);
  }

  /**
   * @covers ::getConfiguration
   */
  public function testConfigurationOverridesDefaults(): void {
    $block_id = json_encode(['view_id' => 'frontpage', 'display_id' => 'block_1']);
    $plugin = $this->createPlugin(['block_id' => $block_id]);

    $this->assertSame($block_id, $plugin->getConfiguration()['block_id']);
  }

}
