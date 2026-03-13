/**
 * @file Registers the entity embed button(s) to the CKEditor instance(s) and binds functionality to it/them.
 */

// eslint-disable-next-line import/no-unresolved
import { Plugin } from 'ckeditor5/src/core';
// eslint-disable-next-line import/no-unresolved
import { ButtonView } from 'ckeditor5/src/ui';
import { openDialog, getSvg } from './utils';

/**
 * Ck5BlockEmbedUI plugin is responsible for adding entity embed buttons
 * to the CKEditor toolbar based on the editor configuration.
 * Each button is linked to a dialog for inserting or editing a CK5 block embed.
 */
export default class Ck5BlockEmbedUI extends Plugin {
  /**
   * @inheritdoc
   * This plugin requires the Widget plugin to handle widget elements.
   */
  static get requires() {
    return ['Widget'];
  }

  /**
   * Initializes the Ck5BlockEmbedUI plugin.
   * Registers embed buttons in the toolbar based on the editor configuration.
   */
  init() {
    const { editor } = this;
    const command = editor.commands.get('insertCk5BlockEmbed'); // Get the command for inserting CK5 block embeds

    const options = editor.config.get('ck5BlockEmbed'); // Get the plugin configuration
    if (!options) {
      return; // If no configuration is provided, do nothing
    }

    const { buttons } = options; // Get the list of buttons to register

    // Register each button defined in the configuration to the toolbar
    Object.keys(buttons).forEach((id) => {
      editor.ui.componentFactory.add(`ck5BlockEmbed__${id}`, (locale) => {
        const button = buttons[id]; // Get the button settings from the configuration
        const buttonView = new ButtonView(locale); // Create a ButtonView instance

        // Set button properties like label, icon, and CSS class
        buttonView.set({
          label: button.label, // Button label
          icon: getSvg(button.iconUrl), // Button icon, fetched using the helper `getSvg`
          tooltip: TRUE, // Enable tooltip
          class: `ck - button_ck5 - block - embed, ck - button_ck5 - block - embed__${id}`, // Custom class for styling
        });

        // Get dialog settings for the button, if provided
        const dialogSettings = button.dialogSettings || {};
        button.editor_id = editor.id; // Set the editor ID on the button
        dialogSettings.editor_id = editor.id; // Set the editor ID in dialog settings

        // Callback function to open the dialog when the button is clicked
        const callback = () => {
          openDialog(
            button,
            ({ attributes, element }) => {
              editor.execute('insertCk5BlockEmbed', attributes, element); // Execute the command to insert the embed
            },
            dialogSettings, // Pass dialog settings to the `openDialog` function
          );
        };

        // Bind button properties to the command
        buttonView.bind('isOn', 'isEnabled').to(command, 'value', 'isEnabled');
        this.listenTo(buttonView, 'execute', callback); // Listen for button click and trigger callback

        return buttonView; // Return the configured button view
      });
    });
  }

  /**
   * @inheritdoc
   * Returns the name of the plugin.
   */
  static get pluginName() {
    return 'Ck5BlockEmbedUI'; // Plugin name
  }
}
