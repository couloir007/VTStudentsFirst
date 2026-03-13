// Importing necessary CKEditor 5 modules and utility functions.
// eslint-disable-next-line import/no-unresolved
import { Command } from 'ckeditor5/src/core';
import { createCk5BlockEmbed } from './utils';

/**
 * A custom command to insert a CKEditor 5 Block Embed into the editor.
 */
export default class InsertCk5BlockEmbedCommand extends Command {
  /**
   * Executes the command to insert a block embed with given attributes and element reference.
   *
   * @param {Object} attributes - The attributes to apply to the block embed.
   * @param {Object} element - The element reference for additional configuration.
   */
  execute(attributes, element) {
    const { model } = this.editor;

    // Retrieve the ck5BlockEmbedEditing plugin instance to access configuration.
    const ck5BlockEmbedEditing = this.editor.plugins.get(
      'ck5BlockEmbedEditing',
    );

    // Map view-specific data attributes to model attributes using the plugin's configuration.
    const dataAttributeMapping = Object.fromEntries(
      Object.entries(ck5BlockEmbedEditing.attrs).map(([key, value]) => [
        value,
        key,
      ]),
    );

    /**
     * Convert view-specific `data-attributes` to model attributes.
     * The data is returned from \Drupal\ck5_block_embed\Form\Ck5BlockEmbedDialog,
     * which uses keys corresponding to view data attributes. This step maps those
     * keys to their respective model attributes.
     */
    const modelAttributes = Object.fromEntries(
      Object.keys(dataAttributeMapping)
        .filter((attribute) => attributes[attribute]) // Filter attributes present in the provided data.
        .map((attribute) => [
          dataAttributeMapping[attribute], // Map to model attribute names.
          attributes[attribute], // Use the corresponding attribute value.
        ]),
    );

    // Perform model changes to insert the block embed.
    model.change((writer) => {
      model.insertContent(
        createCk5BlockEmbed(writer, modelAttributes, element),
      );
    });
  }
}
