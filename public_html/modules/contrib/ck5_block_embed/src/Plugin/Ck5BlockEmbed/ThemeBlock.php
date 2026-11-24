<?php

namespace Drupal\ck5_block_embed\Plugin\Ck5BlockEmbed;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ck5_block_embed\Attribute\Ck5BlockEmbed;
use Drupal\ck5_block_embed\Ck5BlockEmbedInterface;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin for displaying view blocks.
 */
#[Ck5BlockEmbed(
  id: 'theme_block',
  label: new TranslatableMarkup('Theme Block'),
  description: new TranslatableMarkup('Embed blocks from active theme regions.'),
)]
class ThemeBlock extends Ck5BlockEmbedPluginBase implements Ck5BlockEmbedInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The theme handler.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected ThemeHandlerInterface $themeHandler;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    ConfigFactoryInterface $config_factory,
    ThemeHandlerInterface $theme_handler,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
    $this->themeHandler = $theme_handler;
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
      $container->get('config.factory'),
      $container->get('theme_handler'),
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
    // Get the block_id from configuration.
    $block_id = $this->configuration['block_id'];

    // If a block_id is selected, load and render the block.
    if ($block_id) {
      // Decode the block_id to retrieve the block details.
      $block_details = json_decode($block_id);
      if (!$block_details || !isset($block_details->theme, $block_details->block_id)) {
        return [
          '#markup' => $this->t('Invalid theme block configuration.'),
          '#cache' => ['max-age' => 0],
        ];
      }
      $theme = $block_details->theme;
      $block_id = $block_details->block_id;

      // Load the block by its ID.
      $block = $this->entityTypeManager->getStorage('block')->load($block_id);

      // Ensure the block exists and is assigned to the current theme.
      if ($block && $block->getTheme() == $theme) {
        // Return the block render array; the caller is responsible for
        // rendering it within a proper render context.
        return $this->entityTypeManager
          ->getViewBuilder('block')
          ->view($block);
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

    // Get the current default theme.
    $theme = $this->configFactory->get('system.theme')->get('default');

    // Get the block storage to retrieve block placements in regions.
    $block_storage = $this->entityTypeManager->getStorage('block');

    // Get all the blocks.
    $blocks = $block_storage->loadMultiple();

    // Get block theme layout regions for the current theme.
    // Built from the theme info directly instead of the deprecated
    // system_region_list() so this works on Drupal 10 and 11.
    $theme_regions = array_map(
      // phpcs:ignore Drupal.Semantics.FunctionT.NotLiteralString
      static fn ($label) => new TranslatableMarkup($label),
      $this->themeHandler->getTheme($theme)->info['regions'] ?? [],
    );

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

    // Auto-select the first available option; only fall back to empty
    // placeholder when no blocks are available at all.
    $default_value = $this->configuration['block_id'];
    if (!$default_value && !empty($options)) {
      foreach ($options as $group_options) {
        $default_value = array_key_first($group_options);
        break;
      }
    }

    $form['block_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a Block'),
      '#options' => $options,
      '#required' => TRUE,
      '#default_value' => $default_value,
      '#description' => $this->t('Select a block to display. The list will include blocks available in the current theme, which is "@theme" by default. These blocks are predefined and ready for use within your theme layout.', ['@theme' => $theme]),
    ];
    if (empty($options)) {
      $form['block_id']['#empty_option'] = $this->t('- No blocks available -');
    }

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
