<?php

namespace Drupal\ck5_block_embed\Plugin\Filter;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a text filter that turns < ck5-block-embed > tags into markup.
 *
 * @Filter(
 *   id = "ck5_block_embed",
 *   title = @Translation("Embed blocks"),
 *   description = @Translation("Allows embedding blocks from active theme regions, view blocks, and content blocks."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 *   weight = 100,
 * )
 *
 * @internal
 */
class Ck5BlockEmbed extends FilterBase implements ContainerFactoryPluginInterface, TrustedCallbackInterface {

  /**
   * The plugin manager for ck5 block embed.
   *
   * @var \Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager
   */
  protected $ck5BlockEmbedPluginManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Renderer $renderer, Ck5BlockEmbedPluginManager $ck5_block_embed_plugin_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->ck5BlockEmbedPluginManager = $ck5_block_embed_plugin_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
          $configuration,
          $plugin_id,
          $plugin_definition,
          $container->get('renderer'),
          $container->get('plugin.manager.ck5_block_embed')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    // Regular expression to match all <<ck5-block-embed>> tags.
    preg_match_all('/<ck5-block-embed(.*?)>/is', $text, $matches);

    if (empty($matches[0])) {
      // Return early if no matches found.
      return new FilterProcessResult($text);
    }

    foreach ($matches[0] as $match) {
      // Extract plugin config.
      preg_match('/data-plugin-config="([^"]+)"/', $match, $plugin_configs);
      $plugin_config = isset($plugin_configs[1]) ? json_decode(html_entity_decode($plugin_configs[1]), TRUE) : [];

      // Extract plugin ID.
      preg_match('/data-plugin-id="([^"]+)"/', $match, $plugin_id_match);
      $plugin_id = $plugin_id_match[1] ?? NULL;

      if (!$plugin_id) {
        // Skip if plugin ID is not found.
        continue;
      }

      try {
        // Create plugin instance and build the replacement.
        $instance = $this->ck5BlockEmbedPluginManager->createInstance($plugin_id, $plugin_config);
        $replacement = $instance->build();

        // Render the replacement.
        $context = new RenderContext();
        $render = $this->renderer->executeInRenderContext($context, fn() => $this->renderer->render($replacement));
      }
      catch (\Exception $e) {
        // Handle exception and display an error message.
        $render = (new TranslatableMarkup(
              'There is an issue with the ck5 block embed plugin %plugin_id. Error: %error', [
                '%plugin_id' => $plugin_id,
                '%error' => $e->getMessage(),
              ]
          ))->render();
      }

      // Replace the original match with the rendered content.
      $text = str_replace($match, $render, $text);
    }

    // Return the processed text.
    return new FilterProcessResult($text);
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return [];
  }

}
