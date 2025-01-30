<?php

namespace Drupal\nmma_boat_finder_embed\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for rendering the boat finder with query filters.
 */
class BoatFinderEmbedController extends ControllerBase implements ContainerInjectionInterface {
  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->configFactory = $container->get('config.factory');
    return $instance;
  }

  /**
   * Renders the boat finder React app with filters from the query parameters.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object containing query parameters.
   *
   * @return array
   *   A render array with the boat finder element.
   */
  public function boatFinder(Request $request): array {
    $boat_finder_config = $this->configFactory->get('nmma_boat_finder_embed.settings');

    $show_id = $boat_finder_config->get('show_id');
    $infinite_scroll = $boat_finder_config->get('infinite_scroll');
    $max_length = $boat_finder_config->get('max_length');
    $max_price = $boat_finder_config->get('max_price');
    $boat_type = $boat_finder_config->get('boat_type');
    $boat_brand = $boat_finder_config->get('boat_brand');
    $show_color = $boat_finder_config->get('show_color');
    $modal_sponsor_tagline = $boat_finder_config->get('modal_sponsor_tagline');
    $modal_sponsor_image = $boat_finder_config->get('modal_sponsor_image');
    $modal_sponsor_link = $boat_finder_config->get('modal_sponsor_link');
    $rows_between_sponsor_cards = $boat_finder_config->get('rows_between_sponsor_cards');
    $sponsor_card_link = $boat_finder_config->get('sponsor_card_link');
    $sponsor_card_image = $boat_finder_config->get('sponsor_card_image');
    $city_location = $boat_finder_config->get('city_location');
    $show_booth_info = $boat_finder_config->get('show_booth_info');
    $show_exhibitor_info = $boat_finder_config->get('show_exhibitor_info');

    // Build the render array using the custom render element.
    $build = [
      '#type' => 'boat_finder_embed',
      '#paged' => $infinite_scroll,
      '#boat_type' => $boat_type,
      '#boat_brand' => $boat_brand,
      '#city_location' => $city_location,
      '#show_id' => $show_id,
      '#max_length' => $max_length,
      '#max_price' => $max_price,
      '#show_color' => $show_color,
      '#modal_sponsor_tagline' => $modal_sponsor_tagline,
      '#modal_sponsor_image' => $modal_sponsor_image,
      '#modal_sponsor_link' => $modal_sponsor_link,
      '#rows_between_sponsor_cards' => $rows_between_sponsor_cards,
      '#sponsor_card_link' => $sponsor_card_link,
      '#sponsor_card_image' => $sponsor_card_image,
      '#show_booth_info' => $show_booth_info,
      '#show_exhibitor_info' => $show_exhibitor_info,
    ];

    // Return the render array.
    return $build;
  }

}
