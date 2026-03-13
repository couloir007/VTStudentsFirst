<?php

namespace Drupal\ck5_block_embed\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\editor\Ajax\EditorDialogSave;
use Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager;
use Drupal\filter\Entity\FilterFormat;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\NestedArray;

/**
 * Ckeditor dialog form to insert webform submission results in text.
 */
class Ck5BlockEmbedDialogForm extends FormBase {

  const DIALOG_WIDTH = '600px';

  const DIALOG_HEIGHT = 'auto';

  const DIALOG_RENDERER = 'modal';

  /**
   * The ck5 block embed plugin manager.
   *
   * @var \Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager
   */
  protected $ck5BlockEmbedPluginManager;

  /**
   * The ajax wrapper id to use for re-rendering the form.
   *
   * @var string
   */
  protected $ajaxWrapper = 'ck5-block-embed-dialog-form-wrapper';

  /**
   * The modal selector.
   *
   * @var string
   */
  protected $modalSelector = '';

  /**
   * The form constructor.
   *
   * @param \Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager $ck5_block_embed_plugin_manager
   *   The ck5 block embed plugin manager.
   */
  public function __construct(Ck5BlockEmbedPluginManager $ck5_block_embed_plugin_manager) {
    $this->ck5BlockEmbedPluginManager = $ck5_block_embed_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.ck5_block_embed')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ck5_block_embed_dialog_form';
  }

  /**
   * Access callback for ck5 block embed.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function checkAccess(AccountProxyInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'use ck5 block embed button');
  }

  /**
   * Get the available ck5 block embed plugins filtered by the button settings.
   *
   * @return \Drupal\Component\Plugin\Definition\PluginDefinitionInterface[]
   *   The plugin definitions.
   */
  protected function getAvailablePluginDefinitions(): array {
    $definitions = $this->ck5BlockEmbedPluginManager->getDefinitions();
    return $definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?FilterFormat $filter_format = NULL) {
    $payload = $this->getRequest()->getPayload();

    $editor_id = $payload->get('editor_id') ?? $form_state->getValue('editor_id');
    $form['editor_id'] = [
      '#type' => 'hidden',
      '#default_value' => $editor_id ?? '',
    ];

    $this->ajaxWrapper .= '-default';
    $this->modalSelector = '#ck5-block-embed-dialog-form-' . $editor_id;

    $form['#modal_selector'] = $this->modalSelector;

    $plugin_config = $form_state->getValue(['config', 'plugin_config']) ?? Json::decode(Xss::filter($payload->get('plugin_config') ?? '')) ?? $form_state->getUserInput()['config']['plugin_config'] ?? [];
    $plugin_id = $form_state->getValue(['config', 'plugin_id']) ?? $payload->get('plugin_id') ?? $form_state->getUserInput()['config']['plugin_id'] ?? NULL;

    $definitions = $this->getAvailablePluginDefinitions();

    if (!$definitions) {
      $form['warning'] = [
        '#type' => 'markup',
        '#markup' => $this->t('No ck5 block embed plugins were found. Enable the examples module to see some examples or revise if the filter conditions in the button configration are met.'),
      ];
      return $form;
    }
    if ($plugin_id && !isset($definitions[$plugin_id])) {
      $form['warning'] = [
        '#type' => 'markup',
        '#markup' => $this->t('The plugin used for this ck5 block embed is not enabled. Please revise the button settings.'),
      ];
      return $form;
    }
    if (count($definitions) === 1 && !$plugin_id) {
      $plugin_id = array_key_first($definitions);
    }

    $form['button'] = [
      '#type' => 'value',
      '#value' => 'default',
    ];

    $form['config'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => [
        'id' => $this->ajaxWrapper,
      ],

      'plugin_id' => count($definitions) > 1 ? [
        '#type' => 'select',
        '#title' => $this->t('Block category'),
        // '#empty_option' => $this->t('- Select a type -'),
        '#default_value' => $plugin_id,
        '#description' => $this->t('Choose the type of block you would like to embed from the available options.'),
        '#options' => array_map(
          function ($definition) {
            return $definition['label'];
          }, $definitions
        ),
        '#required' => TRUE,
        '#ajax' => [
          'callback' => [$this, 'updateFormElement'],
          'event' => 'change',
          'wrapper' => $this->ajaxWrapper,

        ],
      ] : [
        '#type' => 'value',
        '#value' => $plugin_id,
      ],
    ];
    // Ensure the container for plugin configuration exists.
    $form['config']['plugin_config'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];
    if ($plugin_id) {
      /**
       * @var \Drupal\ck5_block_embed\Ck5BlockEmbedInterface $instance
       */
      try {
        $instance = $this->ck5BlockEmbedPluginManager->createInstance(
          $plugin_id,
          $plugin_config[$plugin_id] ?? []
        );

        // Create the child container BEFORE building the subform.
        $form['config']['plugin_config'][$plugin_id] = [
          '#type' => 'container',
          '#tree' => TRUE,
        ];

        $subform_state = SubformState::createForSubform(
          $form['config']['plugin_config'][$plugin_id],
          $form,
          $form_state
        );

        $form['config']['plugin_config'][$plugin_id] =
          $instance->buildConfigurationForm($form['config']['plugin_config'][$plugin_id], $subform_state);
      }
      catch (\Exception $exception) {
        $this->messenger()->addError($exception->getMessage());
        $form['config']['messages'] = [
          '#type' => 'status_messages',
        ];
      }
    }

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'button',
        '#value' => $this->t('Embed block'),
        '#ajax' => [
          'callback' => [$this, 'ajaxSubmitForm'],
          'wrapper' => $this->ajaxWrapper,
          'disable-refocus' => TRUE,
        ],
      ],
    ];

    return $form;
  }

  /**
   * Return the title for the dialog.
   *
   * @param \Drupal\filter\Entity\FilterFormat|null $filter_format
   *   The filter format.
   */
  public function title(?FilterFormat $filter_format = NULL): TranslatableMarkup|string {
    return $this->t('Embed Block to Editor');
  }

  /**
   * Update the form after selecting a plugin type.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The form element for webform elements.
   */
  public function updateFormElement(array $form, FormStateInterface $form_state): array {
    return $form['config'];
  }

  /**
   * Ajax submit callback to insert or replace the html in ckeditor.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse|array
   *   Ajax response for injecting html in ckeditor.
   */
  public function ajaxSubmitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getErrors()) {
      return $form['config'];
    }
    $plugin_id = $form_state->getValue(['config', 'plugin_id']);

    $plugin_config = $form_state->getValue(
      [
        'config',
        'plugin_config',
        $plugin_id,
      ]
    ) ?? [];

    /**
     * @var \Drupal\ck5_block_embed\Ck5BlockEmbedPluginManager $pluginManager
     */
    $pluginManager = $this->ck5BlockEmbedPluginManager;

    /**
     * @var \Drupal\ck5_block_embed\Ck5BlockEmbedInterface $instance
     */
    $instance = $pluginManager->createInstance($plugin_id, $plugin_config);
    $instance->massageFormValues($plugin_config, $form, $form_state);
    $response = new AjaxResponse();

    $response->addCommand(
      new EditorDialogSave(
        [
          'element' => $instance->isInline() ? 'ck5BlockEmbedInline' : 'ck5BlockEmbed',
          'attributes' => [
            'data-plugin-id' => $plugin_id,
            'data-plugin-config' => Json::encode($plugin_config ?? []),
            'data-button-id' => 'default',
          ],
        ],
        $this->modalSelector,
      )
    );

    if ($this->getDialogSetting('renderer') === 'off_canvas') {
      $response->addCommand(new CloseDialogCommand('#drupal-off-canvas'));
    }
    else {
      $response->addCommand(new CloseModalDialogCommand(FALSE, $this->modalSelector));
    }
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function getDialogSetting(string $setting): ?string {
    return $this->getDialogSettings()[$setting] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getDialogSettings(): array {
    $dialog_settings = [];
    $dialog_settings = NestedArray::filter($dialog_settings);
    return $dialog_settings + [
      'width' => self::DIALOG_WIDTH,
      'height' => self::DIALOG_HEIGHT,
      'title' => $this->t(
          'Create @label', [
            '@label' => 'Embed Block',
          ]
      ),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /**
     * @var \Drupal\ck5_block_embed\Ck5BlockEmbedInterface $instance
     */
    $plugin_id = $form_state->getValue(['config', 'plugin_id']);
    if ($plugin_id) {
      try {
        $instance = $this->ck5BlockEmbedPluginManager->createInstance(
          $plugin_id, $form_state->getValue(
          [
            'config',
            'plugin_config',
          ]
        ) ?? []
        );
        $subform = $form['config']['plugin_config'][$plugin_id] ?? [];
        $subform_state = SubformState::createForSubform($subform, $form, $form_state);
        $instance->validateConfigurationForm($subform, $subform_state);
        if ($subform_state->getErrors()) {
          return $subform;
        }
        $config = $form_state->getValue('config');
        $form_state->setValue('config', $config);
      }
      catch (\Exception $exception) {
        $form_state->setValue('config', []);
      }
    }
    else {
      $form_state->setValue('config', []);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Don't do anything.
  }

}
