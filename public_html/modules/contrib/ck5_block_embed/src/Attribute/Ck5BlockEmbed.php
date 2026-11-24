<?php

declare(strict_types=1);

namespace Drupal\ck5_block_embed\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a CK5 Block Embed plugin attribute.
 *
 * Plugin namespace: Plugin\Ck5BlockEmbed.
 *
 * @see \Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager
 * @see \Drupal\ck5_block_embed\Ck5BlockEmbedInterface
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Ck5BlockEmbed extends Plugin {

  /**
   * Constructs a Ck5BlockEmbed attribute.
   *
   * @param string $id
   *   The plugin ID.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $label
   *   The human-readable name of the plugin.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $description
   *   (optional) A brief description of the plugin.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   */
  public function __construct(
    public readonly string $id,
    public readonly TranslatableMarkup $label,
    public readonly ?TranslatableMarkup $description = NULL,
    public readonly ?string $deriver = NULL,
  ) {}

}
