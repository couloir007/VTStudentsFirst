<?php

declare(strict_types=1);

namespace Drupal\Tests\ck5_block_embed\Unit\Plugin\Filter;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\ck5_block_embed\Ck5BlockEmbedInterface;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager;
use Drupal\ck5_block_embed\Plugin\Filter\Ck5BlockEmbed;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterInterface;

/**
 * Unit tests for the Ck5BlockEmbed filter plugin.
 *
 * @group ck5_block_embed
 * @coversDefaultClass \Drupal\ck5_block_embed\Plugin\Filter\Ck5BlockEmbed
 */
class Ck5BlockEmbedFilterTest extends UnitTestCase {

  /**
   * The renderer mock.
   *
   * @var \Drupal\Core\Render\RendererInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected RendererInterface $renderer;

  /**
   * The plugin manager mock.
   *
   * @var \Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager|\PHPUnit\Framework\MockObject\MockObject
   */
  protected Ck5BlockEmbedPluginManager $pluginManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Provide a string translation service so TranslatableMarkup::render()
    // works in the filter's exception catch-block without a full container.
    $container = new ContainerBuilder();
    $container->set('string_translation', $this->getStringTranslationStub());
    \Drupal::setContainer($container);

    $this->renderer = $this->createMock(RendererInterface::class);
    $this->pluginManager = $this->createMock(Ck5BlockEmbedPluginManager::class);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    parent::tearDown();
    // Prevent container leak into other test classes.
    \Drupal::setContainer(new ContainerBuilder());
  }

  /**
   * Creates the filter plugin instance under test.
   *
   * @return \Drupal\ck5_block_embed\Plugin\Filter\Ck5BlockEmbed
   *   The filter plugin.
   */
  protected function createFilter(): Ck5BlockEmbed {
    return new Ck5BlockEmbed(
      [],
      'ck5_block_embed',
      [
        'id' => 'ck5_block_embed',
        'title' => 'Embed blocks',
        'type' => FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
        'provider' => 'ck5_block_embed',
      ],
      $this->renderer,
      $this->pluginManager,
    );
  }

  /**
   * @covers ::process
   */
  public function testProcessReturnsUnchangedTextWhenNoEmbedTags(): void {
    $text = '<p>Hello world, no embeds here.</p>';
    $result = $this->createFilter()->process($text, 'en');

    $this->assertInstanceOf(FilterProcessResult::class, $result);
    $this->assertSame($text, $result->getProcessedText());
  }

  /**
   * @covers ::process
   */
  public function testProcessReplacesEmbedTagWithRenderedOutput(): void {
    $tag = '<ck5-block-embed data-plugin-id="test_plugin" data-plugin-config="{}">';
    $text = '<p>' . $tag . '</p>';

    $plugin = $this->createMock(Ck5BlockEmbedInterface::class);
    $plugin->method('build')->willReturn(['#markup' => 'Block output']);

    $this->pluginManager->method('createInstance')
      ->with('test_plugin', [])
      ->willReturn($plugin);

    // Simulate executeInRenderContext by just calling the callable.
    $this->renderer->method('executeInRenderContext')
      ->willReturnCallback(function (RenderContext $context, callable $fn) {
        return $fn();
      });
    $this->renderer->method('render')->willReturn('Block output');

    $result = $this->createFilter()->process($text, 'en');

    $this->assertInstanceOf(FilterProcessResult::class, $result);
    $this->assertStringContainsString('Block output', $result->getProcessedText());
    $this->assertStringNotContainsString('<ck5-block-embed', $result->getProcessedText());
  }

  /**
   * Attachments and cache metadata from the render context must bubble up.
   *
   * Regression test for SDC CSS/JS libraries being dropped (#3529868).
   *
   * @covers ::process
   */
  public function testProcessBubblesAttachmentsAndCacheMetadata(): void {
    $text = '<ck5-block-embed data-plugin-id="test_plugin" data-plugin-config="{}">';

    $plugin = $this->createMock(Ck5BlockEmbedInterface::class);
    $plugin->method('build')->willReturn(['#markup' => 'Block output']);
    $this->pluginManager->method('createInstance')->willReturn($plugin);

    // Simulate the renderer pushing bubbled metadata onto the render context.
    $this->renderer->method('executeInRenderContext')
      ->willReturnCallback(function (RenderContext $context, callable $fn) {
        $metadata = (new BubbleableMetadata())
          ->setAttachments(['library' => ['olivero/teaser']])
          ->setCacheTags(['block_content:1']);
        $context->push($metadata);
        return $fn();
      });
    $this->renderer->method('render')->willReturn('Block output');

    $result = $this->createFilter()->process($text, 'en');

    $this->assertSame(['library' => ['olivero/teaser']], $result->getAttachments());
    $this->assertContains('block_content:1', $result->getCacheTags());
  }

  /**
   * @covers ::process
   */
  public function testProcessDecodesHtmlEntityEncodedPluginConfig(): void {
    // Attributes in HTML are often HTML-entity-encoded by the browser.
    $config = htmlspecialchars(json_encode(['key' => 'val']), ENT_QUOTES);
    $tag = '<ck5-block-embed data-plugin-id="test_plugin" data-plugin-config="' . $config . '">';

    $plugin = $this->createMock(Ck5BlockEmbedInterface::class);
    $plugin->method('build')->willReturn(['#markup' => 'OK']);

    // Config should be decoded before being passed to createInstance.
    $this->pluginManager->method('createInstance')
      ->with('test_plugin', ['key' => 'val'])
      ->willReturn($plugin);

    $this->renderer->method('executeInRenderContext')
      ->willReturnCallback(fn(RenderContext $ctx, callable $fn) => $fn());
    $this->renderer->method('render')->willReturn('OK');

    $result = $this->createFilter()->process($tag, 'en');
    $this->assertStringNotContainsString('<ck5-block-embed', $result->getProcessedText());
  }

  /**
   * @covers ::process
   */
  public function testProcessSkipsTagWithMissingPluginId(): void {
    // A tag with no data-plugin-id attribute must be left in place.
    $text = '<p><ck5-block-embed data-plugin-config="{}"></p>';

    $result = $this->createFilter()->process($text, 'en');

    $this->assertStringContainsString('<ck5-block-embed', $result->getProcessedText());
    // The plugin manager must never be called.
    $this->pluginManager->expects($this->never())->method('createInstance');
  }

  /**
   * @covers ::process
   */
  public function testProcessRendersErrorOnPluginException(): void {
    $tag = '<ck5-block-embed data-plugin-id="bad_plugin" data-plugin-config="{}">';
    $text = '<p>' . $tag . '</p>';

    $this->pluginManager->method('createInstance')
      ->willThrowException(new \Exception('Plugin not found'));

    $result = $this->createFilter()->process($text, 'en');

    // The tag should be replaced by an error message (not left as a raw tag).
    $this->assertStringNotContainsString('<ck5-block-embed', $result->getProcessedText());
    $this->assertStringContainsString('ck5 block embed plugin', $result->getProcessedText());
  }

  /**
   * @covers ::process
   */
  public function testProcessHandlesMultipleTags(): void {
    $tag_a = '<ck5-block-embed data-plugin-id="plugin_a" data-plugin-config="{}">';
    $tag_b = '<ck5-block-embed data-plugin-id="plugin_b" data-plugin-config="{}">';
    $text = $tag_a . $tag_b;

    $plugin_a = $this->createMock(Ck5BlockEmbedInterface::class);
    $plugin_a->method('build')->willReturn(['#markup' => 'Output A']);

    $plugin_b = $this->createMock(Ck5BlockEmbedInterface::class);
    $plugin_b->method('build')->willReturn(['#markup' => 'Output B']);

    $this->pluginManager->method('createInstance')
      ->willReturnMap([
        ['plugin_a', [], $plugin_a],
        ['plugin_b', [], $plugin_b],
      ]);

    $this->renderer->method('executeInRenderContext')
      ->willReturnCallback(fn(RenderContext $ctx, callable $fn) => $fn());
    $this->renderer->method('render')
      ->willReturnOnConsecutiveCalls('Output A', 'Output B');

    $result = $this->createFilter()->process($text, 'en');

    $this->assertStringContainsString('Output A', $result->getProcessedText());
    $this->assertStringContainsString('Output B', $result->getProcessedText());
    $this->assertStringNotContainsString('<ck5-block-embed', $result->getProcessedText());
  }

  /**
   * @covers ::trustedCallbacks
   */
  public function testTrustedCallbacksReturnsEmptyArray(): void {
    $this->assertSame([], Ck5BlockEmbed::trustedCallbacks());
  }

}
