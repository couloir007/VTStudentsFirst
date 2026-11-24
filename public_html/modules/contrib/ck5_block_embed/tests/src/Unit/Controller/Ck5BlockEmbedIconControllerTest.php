<?php

declare(strict_types=1);

namespace Drupal\Tests\ck5_block_embed\Unit\Controller;

use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\ck5_block_embed\Controller\Ck5BlockEmbedIconController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unit tests for Ck5BlockEmbedIconController.
 *
 * @group ck5_block_embed
 * @coversDefaultClass \Drupal\ck5_block_embed\Controller\Ck5BlockEmbedIconController
 */
class Ck5BlockEmbedIconControllerTest extends UnitTestCase {

  /**
   * The module handler mock.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * Absolute path to the module root resolved from this test file's location.
   *
   * Tests/src/Unit/Controller/ → 4 levels up → module root.
   */
  protected string $moduleRoot;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Resolve the module root from this file's location so the controller
    // can read the real icons/Ck5BlockEmbed.svg during tests.
    $this->moduleRoot = dirname(__FILE__, 5);
    $this->moduleHandler = $this->createMock(ModuleHandlerInterface::class);
  }

  /**
   * Creates the controller under test.
   *
   * Uses a mocked module handler that returns the real module root path.
   *
   * @return \Drupal\ck5_block_embed\Controller\Ck5BlockEmbedIconController
   *   The controller instance.
   */
  protected function createController(): Ck5BlockEmbedIconController {
    $extension = $this->createMock(Extension::class);
    $extension->method('getPath')->willReturn($this->moduleRoot);

    $this->moduleHandler->method('getModule')
      ->with('ck5_block_embed')
      ->willReturn($extension);

    return new Ck5BlockEmbedIconController($this->moduleHandler);
  }

  /**
   * @covers ::build
   */
  public function testBuildReturnsSvgResponse(): void {
    $response = $this->createController()->build();

    $this->assertInstanceOf(Response::class, $response);
    $this->assertSame(200, $response->getStatusCode());
    $this->assertSame('image/svg+xml', $response->headers->get('Content-Type'));
    $this->assertNotEmpty($response->getContent());
  }

  /**
   * @covers ::getIconSvg
   */
  public function testGetIconSvgReturnsSvgContent(): void {
    $svg = $this->createController()->getIconSvg();

    $this->assertIsString($svg);
    $this->assertNotEmpty($svg);
    $this->assertStringContainsString('<svg', $svg);
  }

  /**
   * @covers ::checkAccess
   */
  public function testCheckAccessAlwaysAllows(): void {
    // Access to the icon endpoint is always granted; no permission needed.
    $account = $this->createMock(AccountProxyInterface::class);
    $result = $this->createController()->checkAccess($account);

    $this->assertTrue($result->isAllowed());
  }

}
