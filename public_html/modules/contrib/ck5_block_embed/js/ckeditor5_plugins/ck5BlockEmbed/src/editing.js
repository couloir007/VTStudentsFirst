// Importing necessary CKEditor 5 modules and commands.
// eslint-disable-next-line import/no-unresolved
import { Plugin } from 'ckeditor5/src/core';
// eslint-disable-next-line import/no-unresolved
import { Widget, toWidget } from 'ckeditor5/src/widget';
import InsertCk5BlockEmbedCommand from './command';

/**
 * Custom CKEditor 5 plugin for handling the editing functionality of CK5 Block Embed.
 * This plugin manages the schema, conversions, and interaction with Drupal entities.
 */
export default class Ck5BlockEmbedEditing extends Plugin {
  /**
   * Defines the plugin dependencies. In this case, it requires the Widget plugin.
   * @returns {Array} The required plugins.
   */
  static get requires() {
    return [Widget];
  }

  /**
   * Initializes the plugin by setting up configuration options, attributes, error messages,
   * defining the schema and converters, and adding the 'insertCk5BlockEmbed' command.
   */
  init() {
    // Define the attributes that will be associated with the block embed.
    this.attrs = {
      dataPluginConfig: 'data-plugin-config',
      dataPluginId: 'data-plugin-id',
      dataButtonId: 'data-button-id',
    };

    // Retrieve plugin configuration from the editor's config. Throws an error if not defined.
    const options = this.editor.config.get('ck5BlockEmbed');
    if (!options) {
      throw new Error(
        'Error on initializing ck5BlockEmbed plugin: ck5BlockEmbed config is required.',
      );
    }
    this.options = options;

    // Error messages for previewing the block embed.
    this.labelError = Drupal.t('Preview failed');
    this.previewError = `
      < p > ${Drupal.t(
        'An error occurred while trying to preview the ck5 block embed. Please save your work and reload this page.',
      )} < p >
    `;

    // Define the schema and converters for the editor.
    this._defineSchema();
    this._defineConverters();

    // Add the custom command to insert a CK5 block embed.
    this.editor.commands.add(
      'insertCk5BlockEmbed',
      new InsertCk5BlockEmbedCommand(this.editor),
    );
  }

  /**
   * Registers the CK5 block embed as a block element in the editor's schema.
   * Defines the allowed attributes and where the block embed can appear.
   * @private
   */
  _defineSchema() {
    const { schema } = this.editor.model;

    // Register a block element for the CK5 Block Embed with specific attributes.
    schema.register('ck5BlockEmbed', {
      isObject: TRUE,
      isContent: TRUE,
      isBlock: TRUE,
      allowWhere: '$block',
      allowAttributes: Object.keys(this.attrs),
    });

    // Register an inline version of the CK5 Block Embed.
    schema.register('ck5BlockEmbedInline', {
      isObject: TRUE,
      isContent: TRUE,
      isBlock: TRUE,
      isInline: TRUE,
      allowWhere: '$text',
      allowAttributes: Object.keys(this.attrs),
    });

    // Add the custom DOM elements to the converter for block and inline block embeds.
    this.editor.editing.view.domConverter.blockElements.push(
      'ck5-block-embed',
    );
    this.editor.editing.view.domConverter.blockElements.push(
      'ck5-block-embed-inline',
    );
  }

  /**
   * Defines the conversion rules for upcasting and downcasting the CK5 Block Embed.
   * Also handles preview rendering and attribute conversions.
   * @private
   */
  _defineConverters() {
    const { conversion } = this.editor;

    // Mapping of view element names to model element names for conversion.
    const displayTypeMapping = {
      'ck5-block-embed': 'ck5BlockEmbed',
      'ck5-block-embed-inline': 'ck5BlockEmbedInline',
    };

    // Loop through each view to model conversion pair.
    Object.entries(displayTypeMapping).forEach(([viewName, modelName]) => {
      // Upcasting: Convert view element to model element.
      conversion.for('upcast').elementToElement({
        view: {
          name: viewName,
        },
        model: modelName,
      });

      // Data Downcasting: Convert model element to view element for the data pipeline.
      conversion.for('dataDowncast').elementToElement({
        model: modelName,
        view: {
          name: viewName,
        },
      });

      // Editing Downcasting: Convert model element to view element for the editor.
      conversion
        .for('editingDowncast')
        .elementToElement({
          model: modelName,
          view: (modelElement, { writer }) => {
            const container = writer.createContainerElement('figure', {
              class: `ck5 - block - embed - preview - wrapper ${viewName}`,
            });
            writer.setCustomProperty('ck5BlockEmbed', TRUE, container);
            return toWidget(container, writer, {
              label: Drupal.t('Ck5 block embed'),
            });
          },
        })
        .add((dispatcher) => {
          // Handle dynamic preview fetching when the plugin is inserted.
          const converter = (event, data, conversionApi) => {
            const viewWriter = conversionApi.writer;
            const modelElement = data.item;
            const container = conversionApi.mapper.toViewElement(data.item);
            const ck5BlockEmbed = viewWriter.createRawElement('span', {
              'data-ck5-block-embed-preview': 'loading',
              class: `ck5 - block - embed - preview ${viewName}`,
            });
            viewWriter.insert(
              viewWriter.createPositionAt(container, 0),
              ck5BlockEmbed,
            );
            this._fetchPreview(modelElement).then((preview) => {
              if (!ck5BlockEmbed) {
                return;
              }
              this.editor.editing.view.change((writer) => {
                const renderFunction = (domElement) => {
                  domElement.innerHTML = preview;
                };
                const ck5BlockEmbedPreview = writer.createRawElement(
                  'span',
                  {
                    class: `ck5 - block - embed - preview ${viewName}`,
                    'data-ck5-block-embed-preview': 'ready',
                  },
                  renderFunction,
                );
                writer.insert(
                  writer.createPositionBefore(ck5BlockEmbed),
                  ck5BlockEmbedPreview,
                );
                writer.remove(ck5BlockEmbed);
              });
            });
          };
          dispatcher.on(`attribute:dataPluginId:${modelName}`, converter);
          return dispatcher;
        });

      // Map the attributes from the model to the view and vice versa.
      Object.keys(this.attrs).forEach((modelKey) => {
        const attributeMapping = {
          model: {
            key: modelKey,
            name: modelName,
          },
          view: {
            name: viewName,
            key: this.attrs[modelKey],
          },
        };
        conversion.for('dataDowncast').attributeToAttribute(attributeMapping);
        conversion.for('upcast').attributeToAttribute(attributeMapping);
      });
    });
  }

  /**
   * Fetches the preview content for the block embed from the server.
   * @param {HTMLElement} modelElement - The model element to fetch the preview for.
   * @returns {Promise<string>} The HTML content of the preview.
   */
  async _fetchPreview(modelElement) {
    const config = {
      plugin_id: modelElement.getAttribute('dataPluginId'),
      plugin_config: modelElement.getAttribute('dataPluginConfig'),
      editor_id: this.editor.id,
    };
    const response = await fetch(this.options.previewUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(config),
    });
    if (response.ok) {
      return response.text();
    }

    return this.themeError;
  }

  /**
   * Provides the plugin's name to be used by the CKEditor system.
   * @returns {string} The name of the plugin.
   */
  static get pluginName() {
    return 'ck5BlockEmbedEditing';
  }
}
