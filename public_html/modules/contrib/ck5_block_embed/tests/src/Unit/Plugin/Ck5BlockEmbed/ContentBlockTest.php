<?php

declare(strict_types=1);

namespace Drupal\Tests\ck5_block_embed\Unit\Plugin\Ck5BlockEmbed;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\block_content\BlockContentInterface;
use Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed\ContentBlock;

/**
 * Unit tests for the ContentBlock plugin.
 *
 * @group ck5_block_embed
 * @coversDefaultClass \Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed\ContentBlock
 */
class ContentBlockTest extends UnitTestCase {

  /**
   * The entity type manager mock.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
  }

  /**
   * Creates a ContentBlock plugin instance for testing.
   *
   * @param array $configuration
   *   Optional plugin configuration.
   *
   * @return \Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed\ContentBlock
   *   The plugin instance.
   */
  protected function createPlugin(array $configuration = []): ContentBlock {
    $plugin = new ContentBlock(
      $configuration,
      'content_block',
      ['id' => 'content_block', 'label' => 'Content Block', 'description' => ''],
      $this->entityTypeManager,
    );
    $plugin->setStringTranslation($this->getStringTranslationStub());
    return $plugin;
  }

  /**
   * @covers ::defaultConfiguration
   */
  public function testDefaultConfiguration(): void {
    $plugin = $this->createPlugin();
    $this->assertSame(['block_id' => NULL], $plugin->defaultConfiguration());
  }

  /**
   * @covers ::getConfiguration
   */
  public function testConfigurationMergedWithDefaults(): void {
    $plugin = $this->createPlugin(['block_id' => 42]);
    $this->assertSame(42, $plugin->getConfiguration()['block_id']);
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
  public function testBuildReturnsFallbackWhenBlockNotFound(): void {
    $storage = $this->createMock(EntityStorageInterface::class);
    $this->entityTypeManager->method('getStorage')
      ->with('block_content')
      ->willReturn($storage);
    $storage->method('load')->with(99)->willReturn(NULL);

    $build = $this->createPlugin(['block_id' => 99])->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('No content block selected', (string) $build['#markup']);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsFallbackWhenAccessDenied(): void {
    $block_content = $this->createMock(BlockContentInterface::class);
    $block_content->method('access')->with('view')->willReturn(FALSE);

    $storage = $this->createMock(EntityStorageInterface::class);
    $this->entityTypeManager->method('getStorage')
      ->with('block_content')
      ->willReturn($storage);
    $storage->method('load')->with(1)->willReturn($block_content);

    $build = $this->createPlugin(['block_id' => 1])->build();

    $this->assertArrayHasKey('#markup', $build);
    $this->assertStringContainsString('No content block selected', (string) $build['#markup']);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsRenderArrayOnSuccess(): void {
    $expected_render = ['#theme' => 'block_content', '#block_content' => 'mock'];

    $view_builder = $this->createMock(EntityViewBuilderInterface::class);
    $view_builder->method('view')->willReturn($expected_render);

    $block_content = $this->createMock(BlockContentInterface::class);
    $block_content->method('access')->with('view')->willReturn(TRUE);

    $storage = $this->createMock(EntityStorageInterface::class);
    $this->entityTypeManager->method('getStorage')
      ->with('block_content')
      ->willReturn($storage);
    $this->entityTypeManager->method('getViewBuilder')
      ->with('block_content')
      ->willReturn($view_builder);
    $storage->method('load')->with(5)->willReturn($block_content);

    $build = $this->createPlugin(['block_id' => 5])->build();

    $this->assertSame($expected_render, $build);
  }

}
