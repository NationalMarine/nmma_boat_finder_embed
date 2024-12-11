<?php

namespace Drupal\nmma_boat_finder_embed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to embed the Boat Finder React application.
 *
 * This block renders a custom render element that embeds the Boat Finder
 * React app within a Drupal site. The block allows customization of the
 * app's behavior via the `paged`, `boat_type`, and `boat_brand` variables.
 *
 * @Block(
 *   id = "boat_finder_embed_block",
 *   admin_label = @Translation("Boat Finder Embed Block"),
 * )
 */
class BoatFinderEmbedBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The name of the field used to store the boat finder filter value.
   */
  private const FILTER_FIELD_NAME = 'field_boat_finder_filter_value';

  /**
   * Supported taxonomy term bundles for filtering.
   */
  private const SUPPORTED_TERM_BUNDLES = ['boat_types', 'brands'];

  /**
   * The route match service to get parameters from the current route.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $block = new static($configuration, $plugin_id, $plugin_definition);
    $block->routeMatch = $container->get('current_route_match');
    return $block;
  }

  /**
   * Builds the content for the block, embedding the Boat Finder React app.
   *
   * This method defines the render array to embed the React app with
   * customized filtering options for boat types and brands.
   *
   * @return array
   *   A renderable array containing the render element for the Boat Finder app.
   */
  public function build(): array {
    // Define the render array for the block.
    return [
      '#type' => 'boat_finder_embed',
      // Pass specific filter and pagination options to the render element.
      '#paged' => TRUE,
      // Example: Enable infinite scroll.
      '#boat_type' => $this->getBoatFilterValue('boat_types'),
      // Example: Filter by type 'Deck Boats'.
      '#boat_brand' => $this->getBoatFilterValue('boat_brand'),
      // Example: Filter by 'Centurion' brand.
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    // Add 'route' context and any cache contexts from the filter term.
    $entity = $this->getFilterTerm();
    $contexts = $entity ? $entity->getCacheContexts() : [];
    $contexts[] = 'route';
    return Cache::mergeContexts(parent::getCacheContexts(), $contexts);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Add the cache tags of the filter term if applicable.
    $entity = $this->getFilterTerm();
    $cache_tags = $entity ? $entity->getCacheTags() : [];
    return Cache::mergeTags(parent::getCacheTags(), $cache_tags);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // Use the cache max age of the term if applicable.
    $entity = $this->getFilterTerm();
    $max_age = $entity ? $entity->getCacheMaxAge() : Cache::PERMANENT;
    return Cache::mergeMaxAges(parent::getCacheMaxAge(), $max_age);
  }

  /**
   * Retrieves the current taxonomy term from the route, if applicable.
   *
   * This method checks if the current route contains a taxonomy term that
   * is one of the supported bundles and has the filter field populated.
   *
   * @return \Drupal\taxonomy\TermInterface|null
   *   The term if it exists, otherwise NULL.
   */
  private function getFilterTerm(): ?TermInterface {
    $term = $this->routeMatch->getParameter('taxonomy_term');
    if (!$term) {
      return NULL;
    }
    if (!in_array($term->bundle(), self::SUPPORTED_TERM_BUNDLES)) {
      return NULL;
    }
    if (!$term->hasField(self::FILTER_FIELD_NAME)) {
      return NULL;
    }
    if ($term->get(self::FILTER_FIELD_NAME)->isEmpty()) {
      return NULL;
    }
    if (!$term->access('view')) {
      return NULL;
    }
    return $term;
  }

  /**
   * Retrieves the filter value from the current term based on the bundle type.
   *
   * This function is used to fetch the filter value from the term's custom
   * field depending on whether the term is a 'boat_types' or 'brands' bundle.
   *
   * @param string $bundle
   *   The term bundle to filter by ('boat_types' or 'brands').
   *
   * @return string|null
   *   The filter value if available, otherwise NULL.
   */
  private function getBoatFilterValue(string $bundle): ?string {
    $term = $this->getFilterTerm();
    if (!$term) {
      return NULL;
    }
    if ($term->bundle() != $bundle) {
      return NULL;
    }
    return $term->get(self::FILTER_FIELD_NAME)->getString();
  }

}
