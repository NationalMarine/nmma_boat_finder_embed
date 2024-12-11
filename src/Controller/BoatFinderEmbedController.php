<?php

namespace Drupal\nmma_boat_finder_embed\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for rendering the boat finder with query filters.
 */
class BoatFinderEmbedController extends ControllerBase {

  /**
   * Renders the boat finder React app with filters from the query parameters.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object containing query parameters.
   *
   * @return array
   *   A render array with the boat finder element.
   */
  public function findBoatsByBrand(Request $request): array {
    // Get query parameters.
    $boat_type = $request->query->get('boat-type', '');
    $boat_brand = $request->query->get('boat-brand', '');
    $analytics_citylocation = $request->query->get('analyticsCitylocation', '');

    // Build the render array using the custom render element.
    $build = [
      '#type' => 'boat_finder_embed',
      '#paged' => TRUE,
      '#boat_type' => $boat_type,
      '#boat_brand' => $boat_brand,
      '#analytics_citylocation' => $analytics_citylocation,
    ];

    // Set cache metadata.
    $cache_metadata = new CacheableMetadata();
    // Cache this response based on the query parameters (boat type and brand).
    $cache_metadata->setCacheContexts([
      'url.query_args:boat-type',
      'url.query_args:boat-brand',
      'url.query_args:analyticsCitylocation',
    ]);
    // Optionally set a cache max-age (0 for no cache, or a time limit in seconds).
    $cache_metadata->setCacheMaxAge(CacheBackendInterface::CACHE_PERMANENT);
    // Attach the cache metadata to the render array.
    $cache_metadata->applyTo($build);
    // Return the render array.
    return $build;
  }

}
