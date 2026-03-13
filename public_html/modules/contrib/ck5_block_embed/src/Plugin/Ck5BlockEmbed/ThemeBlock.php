<?php

namespace Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ck5_block_embed\Ck5BlockEmbedInterface;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginBase;

/**
 * Plugin for displaying view blocks.
 *
 * @Ck5BlockEmbed(
 *   id = "theme_block",
 *   label = @Translation("Theme Block"),
 *   description = @Translation("Embed blocks from active theme regions."),
 * )
 */
class ThemeBlock extends Ck5BlockEmbedPluginBase implements Ck5BlockEmbedInterface {

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
      // Decode the block_id to retrieve the block details.
      $block_details = json_decode($block_id);
      $theme = $block_details->theme;
      $block_id = $block_details->block_id;

      // Load the block by its ID.
      $block_storage = \Drupal::entityTypeManager()->getStorage('block');
      $block = $block_storage->load($block_id);

      // Ensure the block exists and is assigned to the current theme.
      if ($block && $block->getTheme() == $theme) {
        // Render the block.
        $block_content = \Drupal::entityTypeManager()
          ->getViewBuilder('block')
          ->view($block);

        return ['#markup' => \Drupal::service('renderer')->renderRoot($block_content)];

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
    // Initialize the options array for the select.
    $options = [];

    // Get the current deafualt theme.
    $config = \Drupal::config('system.theme');
    $theme = $config->get('default');

    // Get the block layout service to retrieve block placements in regions.
    $block_storage = \Drupal::entityTypeManager()->getStorage('block');

    // Get all the blocks.
    $blocks = $block_storage->loadMultiple();

    // Get block theme layout regions for the current theme.
    $theme_regions = system_region_list($theme, $show = REGIONS_ALL);

    // Add a blank option as the first element in the options array.
    foreach ($blocks as $block) {
      // Check if the block is assigned to the active theme.
      if ($block->getTheme() == $theme) {
        // Retrieve block region assignments.
        $block_region = $block->getRegion();

        // Retrieve the region label from theme block layout.
        $region_label = isset($theme_regions[$block_region]) ? $theme_regions[$block_region]->__toString() : $block_region;

        // Prepare the block value as a JSON-encoded string.
        $block_value = json_encode([
          'theme' => $theme,
          'block_id' => $block->id(),
        ]);

        // Add block to options.
        $options[$region_label][$block_value] = $block->label();
      }
    }

    $form['block_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a Block'),
      '#options' => $options,
      '#required' => TRUE,
      '#default_value' => $this->configuration['block_id'],
      '#description' => $this->t('Select a block to display. The list will include blocks available in the current theme, which is "@theme" by default. These blocks are predefined and ready for use within your theme layout.', ['@theme' => $theme]),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Save the selected block ID into the plugin configuration.
    $this->configuration['block_id'] = $form_state->getValue('block_id');
  }

  /**
   * {@inheritdoc}
   */
  public function isInline(): bool {
    return FALSE;
  }

}
