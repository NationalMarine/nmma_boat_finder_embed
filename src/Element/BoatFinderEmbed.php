<?php

namespace Drupal\nmma_boat_finder_embed\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a render element to embed the Boat Finder React application.
 *
 * This render element outputs the HTML needed to serve the Boat Finder React
 * app and attaches the required JS and CSS libraries. The render element
 * accepts the following variables:
 * - paged: (bool) Whether to enable infinite scroll (default: FALSE).
 * - boat_type: (string) The type of boat to filter by.
 * - boat_brand: (string) The brand of boat to filter by.
 * - show_booth_info: (string) The booth info to display on boat card.
 * - show_exhibitor_info: (string) The exhibitor info to display in boat card modal.
 *
 * @RenderElement("boat_finder_embed")
 */
class BoatFinderEmbed extends RenderElement {

  /**
   * {@inheritdoc}
   *
   * Defines the default properties for the render element.
   */
  public function getInfo() {
    return [
      // Default value for whether infinite scroll is enabled.
      '#paged' => FALSE,
      // Default boat type to filter by, can be overridden.
      '#boat_type' => '',
      // Default boat brand to filter by, can be overridden.
      '#boat_brand' => '',
      // Default show booth info to filter by, can be overridden.
      '#show_booth_info' => '',
      // Default show exhibitor info to filter by, can be overridden.
      '#show_exhibitor_info' => '',
      // Define the theme hook used to render the output.
      '#theme' => 'boat_finder_embed',
      // Attach the library that includes the necessary CSS and JS.
      '#attached' => [
        'library' => [
          'nmma_boat_finder_embed/boat_finder_embed',
        ],
      ],
    ];
  }

}
