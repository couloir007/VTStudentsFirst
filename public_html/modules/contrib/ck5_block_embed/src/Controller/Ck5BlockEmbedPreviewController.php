<?php

namespace Drupal\ck5_block_embed\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\editor\Entity\Editor;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller to handle CK5 Block Embed preview functionality.
 *
 * This controller handles requests for generating and rendering a preview
 * of the CK5 Block Embed plugin within the editor. It decodes the request
 * content, processes the plugin data, and returns a response containing
 * the rendered preview markup.
 */
class Ck5BlockEmbedPreviewController extends ControllerBase {

  use StringTranslationTrait;

  /**
   * The renderer service for rendering the preview.
   *
   * This service is used to render the build array into HTML.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * The CK5 Block Embed plugin manager.
   *
   * This service is responsible for managing the CK5 Block Embed plugin
   * instances, allowing us to create and configure the plugin instances.
   *
   * @var \Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager
   */
  protected $ck5BlockEmbedPluginManager;

  /**
   * {@inheritdoc}
   *
   * This method is used to create the controller object with the required
   * services injected via the container.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.ck5_block_embed'),
      $container->get('renderer')
    );
  }

  /**
   * Controller constructor.
   *
   * Initializes the controller with the required services: the CK5 Block
   * Embed Plugin Manager and the Renderer service.
   *
   * @param \Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager $ck5_block_embed_plugin_manager
   *   The CK5 Block Embed plugin manager.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer service for rendering HTML markup.
   */
  public function __construct(Ck5BlockEmbedPluginManager $ck5_block_embed_plugin_manager, Renderer $renderer) {
    $this->ck5BlockEmbedPluginManager = $ck5_block_embed_plugin_manager;
    $this->renderer = $renderer;
  }

  /**
   * Controller callback that handles the preview of the CK5 Block Embed.
   *
   * This method is called when the editor wants to generate a preview for
   * the CK5 Block Embed plugin. It decodes the JSON content from the request,
   * processes the plugin data, and renders the appropriate preview markup.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request object containing the content to be previewed.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response containing the rendered preview.
   */
  public function preview(Request $request) {
    // Decode the JSON content from the request.
    $content = Json::decode($request->getContent());

    // Extract plugin ID and configuration from the content.
    $plugin_id = $content['plugin_id'] ?? NULL;

    try {
      // Ensure the plugin ID is present in the request content.
      if (!$plugin_id) {
        throw new \Exception();
      }

      // Sanitize and process the plugin configuration.
      $plugin_config = isset($content['plugin_config']) ? Xss::filter($content['plugin_config']) : '';
      $plugin_id = Xss::filter($plugin_id);

      // Create an instance of the CK5 Block Embed plugin
      // using the plugin ID and configuration.
      $instance = $this->ck5BlockEmbedPluginManager->createInstance(
        $plugin_id,
        $plugin_config ? Json::decode($plugin_config) : []
      );

      // Build the renderable array for the plugin preview.
      $build = $instance->build();
    }
    catch (\Exception $e) {
      // In case of an error, return an error message instead of the preview.
      $build = [
        'markup' => [
          '#type' => 'markup',
          '#markup' => $this->t('Incorrect configuration. Please recreate this CK5 block embed.'),
        ],
      ];
    }

    // Render the preview and return it as an HTTP response.
    return new Response($this->renderer->renderRoot($build));
  }

  /**
   * Access callback to check permissions for viewing the preview.
   *
   * This method checks if the current user has the required permission to
   * view the preview for the CK5 Block Embed plugin, based on the active
   * text format associated with the editor.
   *
   * @param \Drupal\Core\Session\AccountProxy $account
   *   The current user account.
   * @param \Drupal\editor\Entity\Editor $editor
   *   The editor entity associated with the request.
   *
   * @return \Drupal\Core\Access\AccessResult|\Drupal\Core\Access\AccessResultReasonInterface
   *   The access result, indicating whether the user has permission
   *   to view the preview.
   */
  public function checkAccess(AccountProxy $account, Editor $editor) {
    // Check if the user has the permission to use the active text format.
    return AccessResult::allowedIfHasPermission(
      $account,
      'use text format ' . $editor->getFilterFormat()->id()
    );
  }

}
