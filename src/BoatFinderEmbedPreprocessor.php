<?php

namespace Drupal\nmma_boat_finder_embed;

use Drupal\Core\Url;
use Drupal\taxonomy\TermInterface;
use Drupal\views\Render\ViewsRenderPipelineMarkup;

/**
 * Provides preprocessing functionality for the Boat Finder views and pages.
 *
 * This class encapsulates logic to preprocess and alter the output of views
 * on the Boat Finder page, specifically focusing on appending custom links
 * to rows in the view for filtering boats by brand and type.
 */
final class BoatFinderEmbedPreprocessor {

  /**
   * The name of the field used to store the boat finder filter value.
   */
  private const FILTER_FIELD_NAME = 'field_boat_finder_filter_value';

  /**
   * Alters the output for the 'name' field for the view 'boatfinder_views'.
   *
   * This method modifies the rendered output of a view row to append
   * a custom link that filters boats by type and brand.
   *
   * @param array $variables
   *   The variables array containing field values and row data. The 'output'
   *   key contains the current HTML output, and 'row' contains the view's
   *   result row.
   */
  public function alterBoatBrandAndTypeRow(array &$variables): void {
    $type = $this->getBoatTypeFilterValue($variables);
    $brand = $this->getBoatBrandFilterValue($variables);
    // If either boat type or brand is missing, skip further processing.
    if (empty($type) || empty($brand)) {
      return;
    }
    // Get the current output and append the new filter link.
    $current_output = (string) $variables['output'];
    $new_output = $this->getIconLinkOutput($type, $brand);
    // Combine current and new output.
    $output = $current_output . $new_output;
    // Set the new output.
    $variables['output'] = ViewsRenderPipelineMarkup::create($output);
  }

  /**
   * Builds the HTML for the boat filter icon link.
   *
   * Generates an anchor tag containing a link to filter boats by the specified
   * type and/or brand. The link opens in a new tab and includes a boat filter
   * icon.
   *
   * @param string|null $type
   *   The boat type.
   * @param string|null $brand
   *   The boat brand.
   *
   * @return string
   *   The HTML markup for the boat filter link with the icon.
   */
  private function getIconLinkOutput(string $type = NULL, string $brand = NULL): string {
    $url = $this->getFindBoatsByBrandUrl($type, $brand);
    return "<a class='boat-filter' target='_blank' href='{$url}'><span class='boat-filter-icon'></span></a>";
  }

  /**
   * Generates the URL for filtering boats by brand and/or type.
   *
   * @param string|null $type
   *   The boat type.
   * @param string|null $brand
   *   The boat brand.
   *
   * @return string
   *   The fully generated URL string.
   */
  private function getFindBoatsByBrandUrl(string $type = NULL, string $brand = NULL): string {
    $query = [];
    if (!empty($type)) {
      $query['boat-type'] = $type;
    }
    if (!empty($brand)) {
      $query['boat-brand'] = $brand;
    }
    $url = Url::fromRoute('nmma_boat_finder.find_boats_by_brand', [], ['query' => $query]);
    return $url->toString();
  }

  /**
   * Retrieves the boat type filter value from the view row.
   *
   * This method extracts the boat type filter value from the result row's
   * associated entity, which must be a taxonomy term of the 'boat_types'
   * bundle.
   *
   * @param array $variables
   *   The variables array containing the view's result row data.
   *
   * @return string|null
   *   The boat type filter value, or NULL if the row does not contain a
   *   valid boat type.
   */
  private function getBoatTypeFilterValue(array $variables): ?string {
    /** @var \Drupal\views\ResultRow|null $row */
    $row = $variables['row'] ?? NULL;
    if (!$row) {
      return NULL;
    }
    /** @var \Drupal\taxonomy\TermInterface $term */
    $term = $row->_entity ?? NULL;
    if (!($term instanceof TermInterface) || $term->bundle() !== 'boat_types' || !$term->hasField(self::FILTER_FIELD_NAME)) {
      return NULL;
    }
    $field = $term->get(self::FILTER_FIELD_NAME);
    if ($field->isEmpty()) {
      return NULL;
    }
    return $field->getString();
  }

  /**
   * Retrieves the boat brand filter value from the view row's relationships.
   *
   * This method extracts the boat brand filter value from the result row's
   * relationship entities, which must include a taxonomy term of the 'brands'
   * bundle.
   *
   * @param array $variables
   *   The variables array containing the view's result row data.
   *
   * @return string|null
   *   The boat brand filter value, or NULL if the row does not contain a
   *   valid boat brand.
   */
  private function getBoatBrandFilterValue(array $variables): ?string {
    /** @var \Drupal\views\ResultRow|null $row */
    $row = $variables['row'] ?? NULL;
    if (!$row) {
      return NULL;
    }
    $brands = $row->_relationship_entities ?? NULL;
    if (!$brands) {
      return NULL;
    }
    $term = reset($brands);
    if (!$term instanceof TermInterface || $term->bundle() !== 'brands' || !$term->hasField(self::FILTER_FIELD_NAME)) {
      return NULL;
    }
    $field = $term->get(self::FILTER_FIELD_NAME);
    if ($field->isEmpty()) {
      return NULL;
    }
    return $field->getString();
  }

}
