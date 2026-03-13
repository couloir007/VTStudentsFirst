<?php

namespace Drupal\ck5_block_embed\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefinition;

/**
 * Provides a deriver for ck5 block embed ckeditor5 plugins.
 */
class Ck5BlockEmbedDeriver extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    assert($base_plugin_definition instanceof CKEditor5PluginDefinition);

    $this->derivatives['default'] = new CKEditor5PluginDefinition($this->getPluginDefinition());

    return $this->derivatives;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginDefinition() {
    return [
      'provider' => 'ck5_block_embed',
      'ckeditor5' => [
        'plugins' => [
          'ck5BlockEmbed.Ck5BlockEmbed',
        ],
        'config' => [
          'ck5BlockEmbed' => [],
        ],
      ],
      'drupal' => [
        'label' => 'Ck5 block embed',
        'elements' => [
          '<ck5-block-embed>',
          '<ck5-block-embed-inline>',
          '<ck5-block-embed data-plugin-config data-plugin-id data-button-id>',
          '<ck5-block-embed-inline data-plugin-config data-plugin-id data-button-id>',
        ],
        'admin_library' => $this->getAdminLibrary(),
        'class' => 'Drupal\ck5_block_embed\Plugin\CKEditor5Plugin\Ck5BlockEmbed',
        'library' => 'ck5_block_embed/ck5_block_embed',
        'toolbar_items' => [
          'ck5BlockEmbed__default' => [
            'label' => 'Embed Block',
          ],
        ],
        'conditions' => [
          'filter' => 'ck5_block_embed',
        ],
      ],
    ];
  }

  /**
   * Get the library for the button.
   *
   * @return string
   *   The library name.
   */
  protected function getAdminLibrary(): string {
    return 'ck5_block_embed/admin';
  }

}
