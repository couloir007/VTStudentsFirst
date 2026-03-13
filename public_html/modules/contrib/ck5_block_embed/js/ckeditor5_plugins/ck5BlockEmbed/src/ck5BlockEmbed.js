// Importing the necessary CKEditor 5 modules for the plugin functionality.
// eslint-disable-next-line import/no-unresolved
import { Plugin } from 'ckeditor5/src/core';
import Ck5BlockEmbedEditing from './editing';
import Ck5BlockEmbedToolbar from './toolbar';
import Ck5BlockEmbedUI from './ui';

/**
 * A custom plugin for CKEditor 5 that enables block embedding functionality.
 */
export default class Ck5BlockEmbed extends Plugin {
    /**
     * Specifies the required plugins for this plugin to function correctly.
     * @returns {Array} List of required plugin classes.
     */
    static get requires() {
        return [Ck5BlockEmbedEditing, Ck5BlockEmbedUI, Ck5BlockEmbedToolbar];
    }

    /**
     * Provides the plugin name for CKEditor 5's plugin management system.
     * @returns {string} The name of the plugin.
     */
    static get pluginName() {
        return 'Ck5BlockEmbed';
    }
}
