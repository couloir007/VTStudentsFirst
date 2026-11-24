<?php

declare(strict_types=1);

namespace Drupal\Tests\ck5_block_embed\Unit\Plugin\Ck5BlockEmbed;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\block\BlockInterface;
use Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed\ThemeBlock;

/**
 * Unit tests for the ThemeBlock plugin.
 *
 * @group ck5_block_embed
 * @coversDefaultClass \Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed\ThemeBlock
 */
class ThemeBlockTest extends UnitTestCase {

  /**
   * The entity type manager mock.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The config factory mock.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
  }

  /**
   * Creates a ThemeBlock plugin instance for testing.
   *
   * @param array $configuration
   *   Optional plugin configuration.
   *
   * @return \Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed\ThemeBlock
   *   The plugin instance.
   */
  protected function createPlugin(array $configuration = []): ThemeBlock {
    $plugin = new ThemeBlock(
      $configuration,
      'theme_block',
      ['id' => 'theme_block', 'label' => 'Theme Block', 'description' => ''],
      $this->entityTypeManager,
      $this->configFactory,
      $this->createMock(ThemeHandlerInterface::class),
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
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsErrorOnInvalidJson(): void {
    $build = $this->createPlugin(['block_id' => 'not-valid-json'])->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('Invalid theme block configuration', (string) $build['#markup']);
    $this->assertSame(0, $build['#cache']['max-age']);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsErrorOnJsonMissingKeys(): void {
    // JSON that is valid but missing required 'block_id' key.
    $block_id = json_encode(['theme' => 'mytheme']);
    $build = $this->createPlugin(['block_id' => $block_id])->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('Invalid theme block configuration', (string) $build['#markup']);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsFallbackWhenBlockNotFound(): void {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with('my-block')->willReturn(NULL);
    $this->entityTypeManager->method('getStorage')
      ->with('block')
      ->willReturn($storage);

    $block_id = json_encode(['theme' => 'mytheme', 'block_id' => 'my-block']);
    $build = $this->createPlugin(['block_id' => $block_id])->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('No content block selected', (string) $build['#markup']);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsFallbackWhenThemeMismatch(): void {
    $block = $this->createMock(BlockInterface::class);
    $block->method('getTheme')->willReturn('othertheme');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with('my-block')->willReturn($block);
    $this->entityTypeManager->method('getStorage')
      ->with('block')
      ->willReturn($storage);

    $block_id = json_encode(['theme' => 'mytheme', 'block_id' => 'my-block']);
    $build = $this->createPlugin(['block_id' => $block_id])->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('No content block selected', (string) $build['#markup']);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsRenderArrayOnSuccess(): void {
    $expected_render = ['#theme' => 'block', '#configuration' => []];

    $view_builder = $this->createMock(EntityViewBuilderInterface::class);
    $view_builder->method('view')->willReturn($expected_render);

    $block = $this->createMock(BlockInterface::class);
    $block->method('getTheme')->willReturn('mytheme');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with('my-block')->willReturn($block);

    $this->entityTypeManager->method('getStorage')
      ->with('block')
      ->willReturn($storage);
    $this->entityTypeManager->method('getViewBuilder')
      ->with('block')
      ->willReturn($view_builder);

    $block_id = json_encode(['theme' => 'mytheme', 'block_id' => 'my-block']);
    $build = $this->createPlugin(['block_id' => $block_id])->build();

    $this->assertSame($expected_render, $build);
  }

}
