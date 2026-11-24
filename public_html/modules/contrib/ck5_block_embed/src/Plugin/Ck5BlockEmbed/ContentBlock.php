<?php

namespace Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ck5_block_embed\Attribute\Ck5BlockEmbed;
use Drupal\ck5_block_embed\Ck5BlockEmbedInterface;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin for embedding content blocks.
 */
#[Ck5BlockEmbed(
  id: 'content_block',
  label: new TranslatableMarkup('Content Block'),
  description: new TranslatableMarkup('Content Block.'),
)]
class ContentBlock extends Ck5BlockEmbedPluginBase implements Ck5BlockEmbedInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

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
    $block_id = $this->configuration['block_id'];

    // If a block_id is selected, load and render the block.
    if ($block_id) {
      /** @var \Drupal\block_content\BlockContentInterface|null $block_content */
      $block_content = $this->entityTypeManager
        ->getStorage('block_content')
        ->load($block_id);

      if ($block_content && $block_content->access('view')) {
        // Render the block using the block content view builder.
        $render = $this->entityTypeManager
          ->getViewBuilder('block_content')
          ->view($block_content);
        return $render;
      }
    }

    // Fallback: Return a default message if no block is selected
    // or no block is found.
    return [
      '#markup' => $this->t('No content block selected or the block could not be found.'),
      '#cache' => ['max-age' => 0],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $block_content_storage = $this->entityTypeManager->getStorage('block_content');
    $block_type_storage = $this->entityTypeManager->getStorage('block_content_type');

    // Fetch all content blocks and block types.
    $block_content = $block_content_storage->loadMultiple();
    $block_types = $block_type_storage->loadMultiple();
    $block_options = [];

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

    // Auto-select the first available option; only fall back to an empty
    // placeholder when no content blocks are available at all.
    $default_value = $this->configuration['block_id'];
    if (!$default_value) {
      foreach ($block_options as $group_options) {
        if (!empty($group_options)) {
          $default_value = array_key_first($group_options);
          break;
        }
      }
    }

    // Add the select field with the list of content blocks grouped by type.
    $form['block_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Content Block'),
      '#options' => $block_options,
      '#default_value' => $default_value,
      '#required' => TRUE,
      '#description' => $this->t('Select a block to display. The list will include the available content blocks.'),
    ];
    if (!$default_value) {
      $form['block_id']['#empty_option'] = $this->t('- No content blocks available -');
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['block_id'] = $form_state->getValue('block_id');
  }

  /**
   * {@inheritdoc}
   */
  public function isInline(): bool {
    return FALSE;
  }

}
