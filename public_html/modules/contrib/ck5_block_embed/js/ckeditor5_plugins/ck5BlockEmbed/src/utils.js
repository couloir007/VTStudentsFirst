/**
 * Opens a dialog for embedding a CK5 block and sets up the save callback.
 *
 * @param {Object} button - The button configuration that triggered the dialog.
 * @param {Function} saveCallback - The callback function to save the dialog's input.
 * @param {Object} existingValues - Existing values to submit to the dialog.
 */
export const openDialog = (button, saveCallback, existingValues) => {
  // Create an AJAX dialog instance using the Drupal.ajax method.
  const ckeditorAjaxDialog = Drupal.ajax({
    dialog: {
      width: button.dialogSettings.width, // Set the dialog width.
      height: button.dialogSettings.height, // Set the dialog height.
      class: button.class, // Set additional classes for styling.
    },
    dialogType: button.dialogSettings.renderer === 'off_canvas' ? 'dialog' : 'modal', // Set dialog type based on renderer.
    dialogRenderer: button.dialogSettings.renderer === 'off_canvas' ? 'off_canvas' : NULL, // Set custom renderer for off-canvas dialogs.
    selector: button.dialogSettings.selector, // The selector for the dialog content.
    url: button.dialogUrl, // The URL for the dialog content (typically a Drupal path).
    progress: { type: 'fullscreen' }, // Show fullscreen progress indicator while loading.
    submit: existingValues, // Existing values to be passed with the dialog form submission.
  });

  // Execute the AJAX dialog to open the dialog window.
  ckeditorAjaxDialog.execute();

  // Store the save callback function, which will be invoked when the dialog is closed.
  // If multiple modals are supported, store callbacks in a Map keyed by editor ID.
  if (Drupal.ckeditor5.saveCallback instanceof Map) {
    Drupal.ckeditor5.saveCallback.set('#ck5-block-embed-dialog-form-' + button.editor_id, saveCallback);
  } else {
    Drupal.ckeditor5.saveCallback = saveCallback; // Store the callback directly if not in a Map.
  }
};
