<?php

namespace Drupal\ck5_block_embed\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines the annotation object for the CK5 Block Embed plugin.
 *
 * This annotation is used to provide metadata about the CK5 Block Embed plugin,
 * such as its ID, title, and description.
 *
 * @Annotation
 */
class Ck5BlockEmbed extends Plugin {

  /**
   * The unique identifier for the plugin.
   *
   * This ID is used to reference the plugin in various contexts, such as in
   * configuration or when registering the plugin with the system.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * This title is typically displayed in the user interface and should provide
   * a clear understanding of the plugin's purpose.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

  /**
   * A brief description of the plugin's functionality.
   *
   * This description provides additional context about the plugin's purpose
   * and usage. It may be displayed in the UI or used by other components
   * to provide more details to users or developers.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

}
