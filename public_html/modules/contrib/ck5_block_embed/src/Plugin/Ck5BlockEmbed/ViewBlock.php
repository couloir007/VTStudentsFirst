<?php

namespace Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ck5_block_embed\Attribute\Ck5BlockEmbed;
use Drupal\ck5_block_embed\Ck5BlockEmbedInterface;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginBase;
use Drupal\views\Views;

/**
 * Plugin for displaying view blocks.
 */
#[Ck5BlockEmbed(
  id: 'view_block',
  label: new TranslatableMarkup('View Block'),
  description: new TranslatableMarkup('Displays blocks from a selected view.'),
)]
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
    $block_id = $this->configuration['block_id'];

    if ($block_id) {
      // Safely decode the JSON value; guard against corrupted config.
      $view_details = json_decode($block_id);
      if (!$view_details || !isset($view_details->view_id, $view_details->display_id)) {
        return [
          '#markup' => $this->t('Invalid view block configuration.'),
          '#cache' => ['max-age' => 0],
        ];
      }

      $view_id = $view_details->view_id;
      $display_id = $view_details->display_id;

      $view = Views::getView($view_id);

      // Check that the view exists and the display is accessible.
      if ($view && $view->access($display_id)) {
        $view->setDisplay($display_id);
        $output = $view->render();

        // Ensure the output is always a render array.
        if (!is_array($output)) {
          $output = ['#markup' => $output];
        }

        // Attach config cache tags so the embed is invalidated when the
        // view configuration changes.
        $output['#cache']['tags'] = array_merge(
          $output['#cache']['tags'] ?? [],
          ['config:views.view.' . $view_id]
        );

        return $output;
      }

      return [
        '#markup' => $this->t('The selected view could not be found or is not accessible.'),
        '#cache' => ['max-age' => 0],
      ];
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
    $views = Views::getAllViews();
    $options = [];

    foreach ($views as $view_id => $view) {
      // Skip disabled views – they cannot be rendered on the front end.
      if (!$view->status()) {
        continue;
      }

      $displays = $view->get('display');
      foreach ($displays as $display_id => $display) {
        // Only list block displays.
        if ($display['display_plugin'] !== 'block') {
          continue;
        }
        $json_value = json_encode(['view_id' => $view_id, 'display_id' => $display_id]);
        $options[$view->label()][$json_value] = $display['display_title'];
      }
    }

    // Sort view groups and their options alphabetically.
    ksort($options);
    foreach ($options as &$group) {
      asort($group);
    }
    unset($group);

    // Determine the default value:
    // - Use the saved block_id when editing an existing embed.
    // - Fall back to the first available block when adding a new embed.
    // - Leave NULL (empty option shown) when no view blocks are available.
    $default_value = $this->configuration['block_id'];
    if (!$default_value && !empty($options)) {
      foreach ($options as $group_options) {
        $default_value = array_key_first($group_options);
        break;
      }
    }

    // Define the select element.
    $form['block_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a View Block'),
      '#options' => $options,
      '#required' => TRUE,
      '#default_value' => $default_value,
      '#description' => $this->t('Select a block to display. The list will include the available view blocks.'),
    ];
    // Show an empty placeholder only when no view blocks are available.
    if (empty($options)) {
      $form['block_id']['#empty_option'] = $this->t('- No view blocks available -');
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
