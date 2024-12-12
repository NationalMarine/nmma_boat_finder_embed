/**
 * @file
 * Defines a CKEditor plugin for embedding the Boat Finder App.
 */
(function (CKEDITOR) {

  'use strict';

  /**
   * CKEditor plugin definition for embedding the Boat Finder App.
   */
  CKEDITOR.plugins.add('boat_finder_embed', {
    requires: 'widget',

    /**
     * Initializes the plugin in the CKEditor editor.
     *
     * @param {CKEDITOR.editor} editor
     *   The CKEditor instance being initialized.
     */
    init: function (editor) {

      // Add a command to insert the Boat Finder Embed shortcode.
      editor.addCommand('insertBoatFinderEmbed', {
        exec: function (editor) {
          // Retrieve plugin configuration or use default values.
          const config = editor.config.boatFinderEmbed || {};
          const shortcode = config.default_shortcode || '[boat_finder_app]';
          // Insert the shortcode into the editor.
          editor.insertHtml(shortcode);
        },
      });

      // Iterate through configured buttons and add them to the editor.
      const buttons = editor.config.DrupalBoatFinderEmbedButtons || {};
      for (const key in buttons) {
        if (buttons.hasOwnProperty(key)) {
          const button = buttons[key];
          // Add a button to the CKEditor toolbar.
          editor.ui.addButton(button.id, {
            label: button.label,
            command: 'insertBoatFinderEmbed',
            icon: '/' + button.image,
            modes: { wysiwyg: 1, source: 0 },
          });
        }
      }
    },
  });

})(CKEDITOR);
