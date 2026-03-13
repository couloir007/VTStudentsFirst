<?php

namespace Drupal\ck5_block_embed\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Controller to handle CK5 Block Embed functionality.
 *
 * Includes previewing the block embed button icon and generating related CSS.
 */
class Ck5BlockEmbedIconController extends ControllerBase {

  /**
   * The module handler service.
   *
   * This service is used to interact with Drupal's module system, including
   * retrieving the path of the current module.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructor.
   *
   * Initializes the controller with required services.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service to interact with the module system.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * Creates a binary response containing the icon in SVG format.
   *
   * This method returns the SVG file as a response, which can be used as an
   * image (e.g., for a toolbar button) in the frontend.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   The response containing the SVG content.
   */
  public function build(): Response {
    // Retrieve the SVG content for the embed button icon.
    $svg = $this->getIconSvg();

    // Return the SVG content as a binary response with appropriate headers.
    return new Response(
      $svg, 200, [
        'Content-Type' => 'image/svg+xml',
        'Content-Transfer-Encoding' => 'File Transfer',
      ]
    );
  }

  /**
   * Retrieves the SVG icon file for the CK5 Block Embed button.
   *
   * This method loads the SVG content from the module's 'icons' directory.
   *
   * @return string
   *   The content of the SVG file.
   */
  public function getIconSvg(): string {
    // Get the path to the module's directory.
    $module_path = $this->moduleHandler->getModule('ck5_block_embed')->getPath();

    // Define the full path to the SVG file in the module's 'icons' directory.
    $file_path = $module_path . '/icons/Ck5BlockEmbed.svg';

    // Read and return the file content.
    $content = file_get_contents($file_path);
    return $content;
  }

  /**
   * Creates a binary response containing the generated CSS for the button.
   *
   * This CSS defines the background image for the toolbar button
   * using the icon URL.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   The response containing the generated CSS.
   */
  public function buildAdminCss(): Response {
    // Generate the CSS for the button icon.
    $css = sprintf('.ckeditor5-toolbar-button-ck5BlockEmbed__%s { background-image:url(%s)}', $ck5_block_embed_button->id(), $ck5_block_embed_button->getIconUrl());

    // Return the CSS as a binary response with the correct content type.
    return new Response(
      $css, 200, [
        'Content-Type' => 'text/css',
      ]
    );
  }

  /**
   * Access callback for the CK5 Block Embed functionality.
   *
   * This method checks whether the current user has the required
   * permission to use the CK5 Block Embed button.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The user account to check permissions for.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The result of the access check.
   */
  public function checkAccess(AccountProxyInterface $account) {
    return AccessResult::allowed();
  }

}
