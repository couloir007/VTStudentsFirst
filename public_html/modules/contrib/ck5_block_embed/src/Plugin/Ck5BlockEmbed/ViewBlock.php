<?php

namespace Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ck5_block_embed\Ck5BlockEmbedInterface;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginBase;
use Drupal\views\Views;

/**
 * Plugin for displaying view blocks.
 *
 * @Ck5BlockEmbed(
 *   id = "view_block",
 *   label = @Translation("View Block"),
 *   description = @Translation("Displays blocks from a selected view."),
 * )
 */
class ViewBlock extends Ck5BlockEmbedPluginBase implements Ck5BlockEmbedInterface {

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
      $view_details = json_decode($block_id);
      $view_id = $view_details->view_id;
      $display_id = $view_details->display_id;

      // Get the view object by view ID.
      $view = Views::getView($view_id);

      // Check if the view exists and is loaded.
      if ($view) {
        // Set the display ID (optional, defaults to 'default' display).
        $view->setDisplay($display_id);

        // Execute the view to retrieve the render array.
        $output = $view->render();

        // Return the rendered output (e.g., in a theme or controller).
        return $output;
      }
      else {
        // If the view is not found, return an error message.
        return 'View not found.';
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

    // Get all views that are not attached to pages.
    $views = Views::getAllViews();
    $view_blocks = [];

    foreach ($views as $view_id => $view) {
      // Get all display handlers for the view.
      $displays = $view->get('display');
      foreach ($displays as $display_id => $display) {
        // Only process block displays that are not attached to a page.
        if ($display['display_plugin'] == 'block' && empty($display['page']['path'])) {
          // Create a JSON object for the option value (view_id and display_id).
          $json_value = json_encode(['view_id' => $view_id, 'display_id' => $display_id]);
          $view_blocks[$view->label()][$json_value] = $display['display_title'];
        }
      }
    }

    // Prepare the select options.
    $options = [];
    foreach ($view_blocks as $view_id => $blocks) {
      foreach ($blocks as $json_value => $block_label) {
        $options[$view_id][$json_value] = $block_label;
      }
    }

    // Define the select element.
    $form['block_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a View Block'),
      '#options' => $options,
      '#required' => TRUE,
      '#default_value' => $this->configuration['block_id'],
      '#description' => $this->t('Select a block to display. The list will include the available view blocks.'),
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
