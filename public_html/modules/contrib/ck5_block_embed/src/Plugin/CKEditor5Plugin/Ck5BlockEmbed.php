<?php

declare(strict_types=1);

namespace Drupal\ck5_block_embed\Plugin\CKEditor5Plugin;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefinition;
use Drupal\editor\EditorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\ck5_block_embed\Form\Ck5BlockEmbedDialogForm;

/**
 * Plugin class to add dialog url for ck5 block embed.
 */
class Ck5BlockEmbed extends CKEditor5PluginDefault implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  /**
   * The CSRF token generator.
   *
   * @var \Drupal\Core\Access\CsrfTokenGenerator
   */
  protected $csrfTokenGenerator;

  /**
   * DrupalEntity constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param \Drupal\ckeditor5\Plugin\CKEditor5PluginDefinition $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Entity Type Manager service.
   * @param \Drupal\Core\Access\CsrfTokenGenerator $csrf_token_generator
   *   The CSRF token generator.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    CKEditor5PluginDefinition $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    CsrfTokenGenerator $csrf_token_generator,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->csrfTokenGenerator = $csrf_token_generator;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
          $configuration,
          $plugin_id,
          $plugin_definition,
          $container->get('entity_type.manager'),
          $container->get('csrf_token')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    // Register embed buttons as individual buttons on admin pages.
    $dynamic_plugin_config = $static_plugin_config;
    $url = Url::fromRoute(
          'ck5_block_embed.preview', [
            'editor' => $editor->id(),
          ]
      );
    $token = $this->csrfTokenGenerator->get($url->getInternalPath());
    $url->setOptions(['query' => ['token' => $token]]);
    $dynamic_plugin_config['ck5BlockEmbed']['previewUrl'] = $url->toString();

    $dynamic_plugin_config['ck5BlockEmbed']['buttons']['default'] = [
      'label' => 'Embed Block',
      'iconUrl' => $this->getIconUrl(),
      'dialogUrl' => Url::fromRoute(
          'ck5_block_embed.dialog', [
            'ck5_block_embed_button' => 'default',
            'filter_format' => $editor->getFilterFormat()->id(),
          ]
      )->toString(),
      'dialogSettings' => $this->getDialogSettings(),
    ];

    return $dynamic_plugin_config;
  }

  /**
   * {@inheritdoc}
   */
  public function getDialogSettings(): array {
    $dialog_settings = [];
    $dialog_settings = NestedArray::filter($dialog_settings);
    return $dialog_settings + [
      'width' => Ck5BlockEmbedDialogForm::DIALOG_WIDTH,
      'height' => Ck5BlockEmbedDialogForm::DIALOG_HEIGHT,
      'title' => $this->t(
          'Create @label', [
            '@label' => 'Embed Block',
          ]
      ),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIconUrl(): string {
    $url = Url::fromRoute(
          'ck5_block_embed.ck5_block_embed_button.icon', [
            'ck5_block_embed_button' => 'default',
          ]
      )->setAbsolute();
    return $url->toString();
  }

}
