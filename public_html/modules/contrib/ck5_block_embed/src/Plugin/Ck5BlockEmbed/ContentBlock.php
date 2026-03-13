<?php

namespace Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ck5_block_embed\Ck5BlockEmbedInterface;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginBase;
use Drupal\block_content\Entity\BlockContent;

/**
 * Plugin iframes.
 *
 * @Ck5BlockEmbed(
 *   id = "content_block",
 *   label = @Translation("Content Block"),
 *   description = @Translation("Content Block."),
 * )
 */
class ContentBlock extends Ck5BlockEmbedPluginBase implements Ck5BlockEmbedInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'block_id' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // Get the block_id from configuration.
    $block_id = $this->configuration['block_id'];

    // If a block_id is selected, load and render the block.
    if ($block_id) {
      $block_content = BlockContent::load($block_id);

      if ($block_content) {
        // Render the block using the block content view builder.
        $render = \Drupal::entityTypeManager()->getViewBuilder('block_content')->view($block_content);
        return $render;
      }
    }

    // Fallback: Return a default message if no block is selected
    // or no block is found.
    return [
      '#markup' => $this->t('No content block selected or the block could not be found.'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Fetch all content blocks.
    $block_content = BlockContent::loadMultiple();
    $block_options = [];
    $block_types = \Drupal::entityTypeManager()->getStorage('block_content_type')->loadMultiple();

    // Loop through each block type to group blocks.
    foreach ($block_types as $block_type) {
      $block_type_label = $block_type->label();
      $block_options[$block_type_label] = [];

      // Loop through the blocks and add them to their respective
      // block type group.
      foreach ($block_content as $block) {
        if ($block->bundle() == $block_type->id()) {
          $block_options[$block_type_label][$block->id()] = $block->label();
        }
      }
    }

    // Add the select field with the list of content blocks grouped by type.
    $form['block_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Content Block'),
      '#options' => $block_options,
      '#default_value' => $this->configuration['block_id'],
      '#required' => TRUE,
      '#description' => $this->t('Select a block to display. The list will include the available content blocks.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function isInline(): bool {
    return FALSE;
  }

}
