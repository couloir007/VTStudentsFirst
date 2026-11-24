<?php

declare(strict_types=1);

namespace Drupal\Tests\ck5_block_embed\Unit\Controller;

use Drupal\Core\Cache\Context\CacheContextsManager;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\ck5_block_embed\Ck5BlockEmbedInterface;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager;
use Drupal\ck5_block_embed\Controller\Ck5BlockEmbedPreviewController;
use Drupal\editor\Entity\Editor;
use Drupal\filter\Entity\FilterFormat;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unit tests for Ck5BlockEmbedPreviewController.
 *
 * @group ck5_block_embed
 * @coversDefaultClass \Drupal\ck5_block_embed\Controller\Ck5BlockEmbedPreviewController
 */
class Ck5BlockEmbedPreviewControllerTest extends UnitTestCase {

  /**
   * The plugin manager mock.
   *
   * @var \Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager|\PHPUnit\Framework\MockObject\MockObject
   */
  protected Ck5BlockEmbedPluginManager $pluginManager;

  /**
   * The renderer mock.
   *
   * @var \Drupal\Core\Render\RendererInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected RendererInterface $renderer;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $cache_contexts_manager = $this->getMockBuilder(CacheContextsManager::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['assertValidTokens'])
      ->getMock();
    $cache_contexts_manager->method('assertValidTokens')->willReturn(TRUE);

    $container = new ContainerBuilder();
    $container->set('cache_contexts_manager', $cache_contexts_manager);
    \Drupal::setContainer($container);

    $this->pluginManager = $this->createMock(Ck5BlockEmbedPluginManager::class);
    $this->renderer = $this->createMock(RendererInterface::class);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    parent::tearDown();
    \Drupal::unsetContainer();
  }

  /**
   * Creates the controller under test.
   *
   * The string translation stub is injected so that $this->t() calls inside
   * the controller work without a full Drupal container.
   *
   * @return \Drupal\ck5_block_embed\Controller\Ck5BlockEmbedPreviewController
   *   The controller instance.
   */
  protected function createController(): Ck5BlockEmbedPreviewController {
    $controller = new Ck5BlockEmbedPreviewController(
      $this->pluginManager,
      $this->renderer,
    );
    $controller->setStringTranslation($this->getStringTranslationStub());
    return $controller;
  }

  /**
   * Builds a POST-style Request whose body is the given JSON-serialisable data.
   *
   * @param mixed $data
   *   Data to encode as the request body.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   *   The request object.
   */
  protected function makeRequest(mixed $data): Request {
    return new Request([], [], [], [], [], [], json_encode($data));
  }

  /**
   * @covers ::checkAccess
   */
  public function testCheckAccessAllowedWhenUserHasPermission(): void {
    $filter_format = $this->createMock(FilterFormat::class);
    $filter_format->method('id')->willReturn('full_html');

    $editor = $this->createMock(Editor::class);
    $editor->method('getFilterFormat')->willReturn($filter_format);

    $account = $this->createMock(AccountInterface::class);
    $account->method('hasPermission')
      ->with('use text format full_html')
      ->willReturn(TRUE);

    $result = $this->createController()->checkAccess($account, $editor);

    $this->assertTrue($result->isAllowed());
  }

  /**
   * @covers ::checkAccess
   */
  public function testCheckAccessDeniedWhenUserLacksPermission(): void {
    $filter_format = $this->createMock(FilterFormat::class);
    $filter_format->method('id')->willReturn('full_html');

    $editor = $this->createMock(Editor::class);
    $editor->method('getFilterFormat')->willReturn($filter_format);

    $account = $this->createMock(AccountInterface::class);
    $account->method('hasPermission')
      ->with('use text format full_html')
      ->willReturn(FALSE);

    $result = $this->createController()->checkAccess($account, $editor);

    $this->assertFalse($result->isAllowed());
  }

  /**
   * @covers ::preview
   */
  public function testPreviewRendersPluginBuildArray(): void {
    $build = ['#markup' => '<div>Block content</div>'];

    $plugin = $this->createMock(Ck5BlockEmbedInterface::class);
    $plugin->method('build')->willReturn($build);

    $this->pluginManager->method('createInstance')
      ->with('test_plugin', [])
      ->willReturn($plugin);

    $this->renderer->method('renderRoot')
      ->willReturnCallback(function (&$elements) {
        return '<div>Block content</div>';
      });

    $response = $this->createController()->preview(
      $this->makeRequest(['plugin_id' => 'test_plugin', 'plugin_config' => '{}'])
    );

    $this->assertInstanceOf(Response::class, $response);
    $this->assertSame(200, $response->getStatusCode());
    $this->assertSame('<div>Block content</div>', $response->getContent());
  }

  /**
   * @covers ::preview
   */
  public function testPreviewPassesDecodedPluginConfigToManager(): void {
    $plugin = $this->createMock(Ck5BlockEmbedInterface::class);
    $plugin->method('build')->willReturn(['#markup' => '']);

    // The plugin_config in the request body is a JSON string whose decoded
    // value is ['key' => 'value']; the controller must pass the decoded array.
    $this->pluginManager->expects($this->once())
      ->method('createInstance')
      ->with('test_plugin', ['key' => 'value'])
      ->willReturn($plugin);

    $this->renderer->method('renderRoot')->willReturn('');

    $this->createController()->preview(
      $this->makeRequest([
        'plugin_id' => 'test_plugin',
        'plugin_config' => json_encode(['key' => 'value']),
      ])
    );
  }

  /**
   * @covers ::preview
   */
  public function testPreviewReturnsErrorWhenPluginIdMissing(): void {
    // The plugin manager must never be called.
    $this->pluginManager->expects($this->never())->method('createInstance');

    $this->renderer->method('renderRoot')
      ->willReturnCallback(function (&$elements) {
        return 'Incorrect configuration.';
      });

    $response = $this->createController()->preview(
      $this->makeRequest([])
    );

    $this->assertInstanceOf(Response::class, $response);
    $this->assertSame(200, $response->getStatusCode());
  }

  /**
   * @covers ::preview
   */
  public function testPreviewReturnsErrorOnPluginException(): void {
    $this->pluginManager->method('createInstance')
      ->willThrowException(new \Exception('Plugin not found'));

    $this->renderer->method('renderRoot')
      ->willReturnCallback(function (&$elements) {
        return 'Incorrect configuration.';
      });

    $response = $this->createController()->preview(
      $this->makeRequest(['plugin_id' => 'nonexistent', 'plugin_config' => '{}'])
    );

    $this->assertInstanceOf(Response::class, $response);
    $this->assertSame(200, $response->getStatusCode());
  }

}
