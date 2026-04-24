<?php

namespace Drupal\node_display_title\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

/**
 * Form handler for adding node display title settings.
 */
class NodeDisplayTitleForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'node_display_title_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'node_display_title.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('node_display_title.settings');

    $options = [];
    foreach (NodeType::loadMultiple() as $node_type) {
      $options[$node_type->id()] = $node_type->label();
    }

    $form['bundles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Node bundles'),
      '#description' => $this->t('Select node bundles that should have the "Display title" field.'),
      '#options' => $options,
      '#default_value' => !empty($config->get('bundles')) ? $config->get('bundles') : [],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('node_display_title.settings')
      ->set('bundles', array_filter($form_state->getValue('bundles')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
