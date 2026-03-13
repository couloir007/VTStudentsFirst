// eslint-disable-next-line import/no-unresolved
import { Plugin, icons } from 'ckeditor5/src/core';
// eslint-disable-next-line import/no-unresolved
import { isWidget, WidgetToolbarRepository } from 'ckeditor5/src/widget';
// eslint-disable-next-line import/no-unresolved
import { ButtonView } from 'ckeditor5/src/ui';
import { openDialog } from './utils';

/**
 * Ck5BlockEmbedToolbar is a CKEditor plugin that adds a toolbar button
 * for embedding and editing CK5 block content within the editor.
 *
 * It provides an "Edit" button that, when clicked, opens a dialog to
 * modify the embedded block's attributes and content.
 */
export default class Ck5BlockEmbedToolbar extends Plugin {
  /**
   * @inheritdoc
   * This plugin requires the WidgetToolbarRepository to manage widget tools in the toolbar.
   */
  static get requires() {
    return [WidgetToolbarRepository];
  }

  /**
   * Initializes the toolbar button and its behavior.
   * Adds an "Edit" button to the editor toolbar.
   */
  init() {
    const { editor } = this;
    const options = editor.config.get('ck5BlockEmbed'); // Get configuration options for the plugin

    // Add the "Edit" button to the toolbar
    editor.ui.componentFactory.add('ck5BlockEmbedEdit', (locale) => {
      const buttonView = new ButtonView(locale); // Create a button view for the "Edit" button

      // Set properties for the button
      buttonView.set({
        label: editor.t('Edit'), // Button label
        icon: icons.pencil, // Pencil icon from CKEditor
        tooltip: TRUE, // Display a tooltip
        class: 'ck-button_ck5-block-embed__edit', // Custom class for styling
      });

      // Define the behavior when the button is clicked
      this.listenTo(buttonView, 'execute', () => {
        const selectedElement = editor.model.document.selection.getSelectedElement();

        // Get the button ID from the selected element (if available) or default to 'default'
        const buttonId = selectedElement.getAttribute('dataButtonId') ? ? 'default';
        const button = options.buttons[buttonId]; // Get the corresponding button from the configuration
        button.editor_id = editor.id; // Set the editor ID to ensure the button is tied to the correct editor instance

        const existingValues = {
          plugin_id: selectedElement.getAttribute('dataPluginId'),
          plugin_config: selectedElement.getAttribute('dataPluginConfig'),
          editor_id: editor.id,
        };

        // Open the dialog to edit the CK5 block embed content
        openDialog(
          button,
          ({ attributes, element }) => {
            editor.execute('insertCk5BlockEmbed', attributes, element); // Insert the edited content
            editor.editing.view.focus(); // Refocus the editor
          },
          existingValues, // Pass existing values to the dialog
        );
      });

      return buttonView; // Return the button view
    });
  }

  /**
   * Registers the toolbar item for Ck5BlockEmbed within the widget toolbar.
   * This function is called after the plugin has been initialized.
   */
  afterInit() {
    const { editor } = this;
    if (!editor.plugins.has('WidgetToolbarRepository')) {
      return; // If the WidgetToolbarRepository plugin is not available, return early
    }
    const widgetToolbarRepository = editor.plugins.get(WidgetToolbarRepository); // Get the WidgetToolbarRepository plugin

    // Register the CK5 Block Embed toolbar item
    widgetToolbarRepository.register('ck5BlockEmbed', {
      ariaLabel: Drupal.t('Ck5BlockEmbed toolbar'), // Set the ARIA label for accessibility
      items: ['ck5BlockEmbedEdit'], // Add the "Edit" button to the toolbar
      getRelatedElement(selection) {
        const viewElement = selection.getSelectedElement(); // Get the selected element in the editor
        if (!viewElement || !isWidget(viewElement)) {
          return NULL; // If no widget is selected, return null
        }
        if (!viewElement.getCustomProperty('ck5BlockEmbed')) {
          return NULL; // Ensure that the element is a CK5 Block Embed widget
        }

        return viewElement; // Return the related view element
      },
    });
  }

  /**
   * @inheritdoc
   * Defines the plugin name.
   */
  static get pluginName() {
    return 'Ck5BlockEmbedToolbar'; // Return the plugin name as 'Ck5BlockEmbedToolbar'
  }
}
