<?php

namespace Drupal\Tests\brevo_mailer\Unit;

use Drupal\brevo_mailer\BrevoMailerHandlerInterface;
use Drupal\brevo_mailer\Plugin\Mail\BrevoMail;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Render\RendererInterface;
use Drupal\Tests\UnitTestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests the BrevoMail::buildMessage method.
 *
 * @coversDefaultClass \Drupal\brevo_mailer\Plugin\Mail\BrevoMail
 *
 * @group brevo_mailer
 */
class BrevoMailBuildMessageTest extends UnitTestCase {

  /**
   * The BrevoMail plugin instance.
   *
   * @var \Drupal\brevo_mailer\Plugin\Mail\BrevoMail
   */
  protected $brevoMail;

  /**
   * Mock config object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->config = $this->createMock(ImmutableConfig::class);
    $this->config->method('get')
      ->willReturnMap([
        ['debug_mode', FALSE],
        ['test_mode', FALSE],
        ['format_filter', ''],
        ['use_queue', FALSE],
        ['use_theme', FALSE],
      ]);

    $logger = $this->createMock(LoggerInterface::class);
    $renderer = $this->createMock(RendererInterface::class);
    $queueFactory = $this->createMock(QueueFactory::class);
    $brevoMailerHandler = $this->createMock(BrevoMailerHandlerInterface::class);

    $this->brevoMail = new BrevoMail(
      $this->config,
      $logger,
      $renderer,
      $queueFactory,
      $brevoMailerHandler
    );
  }

  /**
   * Tests buildMessage with a single recipient.
   *
   * @covers ::buildMessage
   */
  public function testBuildMessageSingleRecipient(): void {
    $message = [
      'to' => 'test@example.com',
      'subject' => 'Test Subject',
      'body' => 'Test body content',
      'headers' => [
        'From' => 'sender@example.com',
      ],
    ];

    $result = $this->invokeMethod($this->brevoMail, 'buildMessage', [$message]);

    $this->assertIsArray($result['to']);
    $this->assertCount(1, $result['to']);
    $this->assertEquals('test@example.com', $result['to'][0]['email']);
  }

  /**
   * Tests buildMessage with multiple recipients separated by comma.
   *
   * @covers ::buildMessage
   */
  public function testBuildMessageMultipleRecipients(): void {
    $message = [
      'to' => 'test1@example.com,test2@example.com,test3@example.com',
      'subject' => 'Test Subject',
      'body' => 'Test body content',
      'headers' => [
        'From' => 'sender@example.com',
      ],
    ];

    $result = $this->invokeMethod($this->brevoMail, 'buildMessage', [$message]);

    $this->assertIsArray($result['to']);
    $this->assertCount(3, $result['to']);
    $this->assertEquals('test1@example.com', $result['to'][0]['email']);
    $this->assertEquals('test2@example.com', $result['to'][1]['email']);
    $this->assertEquals('test3@example.com', $result['to'][2]['email']);
  }

  /**
   * Tests buildMessage with multiple recipients separated by comma and space.
   *
   * @covers ::buildMessage
   */
  public function testBuildMessageMultipleRecipientsWithSpaces(): void {
    $message = [
      'to' => 'test1@example.com, test2@example.com, test3@example.com',
      'subject' => 'Test Subject',
      'body' => 'Test body content',
      'headers' => [
        'From' => 'sender@example.com',
      ],
    ];

    $result = $this->invokeMethod($this->brevoMail, 'buildMessage', [$message]);

    $this->assertIsArray($result['to']);
    $this->assertCount(3, $result['to']);
    $this->assertEquals('test1@example.com', $result['to'][0]['email']);
    $this->assertEquals('test2@example.com', $result['to'][1]['email']);
    $this->assertEquals('test3@example.com', $result['to'][2]['email']);
  }

  /**
   * Tests buildMessage with named recipients.
   *
   * @covers ::buildMessage
   */
  public function testBuildMessageNamedRecipients(): void {
    $message = [
      'to' => 'John Doe <john@example.com>,Jane Doe <jane@example.com>',
      'subject' => 'Test Subject',
      'body' => 'Test body content',
      'headers' => [
        'From' => 'sender@example.com',
      ],
    ];

    $result = $this->invokeMethod($this->brevoMail, 'buildMessage', [$message]);

    $this->assertIsArray($result['to']);
    $this->assertCount(2, $result['to']);
    $this->assertEquals('john@example.com', $result['to'][0]['email']);
    $this->assertEquals('John Doe', $result['to'][0]['name']);
    $this->assertEquals('jane@example.com', $result['to'][1]['email']);
    $this->assertEquals('Jane Doe', $result['to'][1]['name']);
  }

  /**
   * Invokes a protected or private method.
   *
   * @param object $object
   *   The object instance.
   * @param string $methodName
   *   The method name.
   * @param array $parameters
   *   The parameters to pass.
   *
   * @return mixed
   *   The return value of the method.
   */
  protected function invokeMethod($object, $methodName, array $parameters = []) {
    $reflection = new \ReflectionClass(get_class($object));
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(TRUE);
    return $method->invokeArgs($object, $parameters);
  }

}
