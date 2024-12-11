<?php

namespace Drupal\nmma_boat_finder_embed\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "Boat Finder Embed" CKEditor plugin.
 *
 * @CKEditorPlugin(
 *   id = "boat_finder_embed",
 *   label = @Translation("Boat Finder Embed"),
 *   module = "nmma_boat_finder_embed"
 * )
 */
final class BoatFinderEmbed extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return $this->getModulePath('nmma_boat_finder_embed') . '/js/ckeditor/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = $this->getModulePath('nmma_boat_finder_embed') . '/js/ckeditor';
    return [
      'boat_finder_embed' => [
        'id' => 'boat_finder_embed',
        'label' => $this->t('Embed Boat Finder App'),
        'image' => $path . '/icon.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [
      'default_shortcode' => 'boat_finder_app',
      'DrupalBoatFinderEmbedButtons' => $this->getButtons(),
    ];
  }

}
