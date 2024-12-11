<?php

namespace Drupal\nmma_boat_finder_embed;

use Drupal\Component\Utility\Random;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BoatFinderEmbedLibraryAlter.
 *
 * This class alters the library definition for the Boat Finder React app
 * embedding. It retrieves the domain and version configuration values and
 * updates the 'boat_finder_embed' library with the appropriate URLs for
 * JavaScript and CSS files.
 */
final class BoatFinderEmbedLibraryAlter implements ContainerInjectionInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The domain for the Boat Finder app.
   *
   * @var string
   */
  protected string $domain;

  /**
   * The version for the Boat Finder app.
   *
   * @var string
   */
  protected string $version;

  /**
   * Constructor for BoatFinderEmbedLibraryAlter.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
    // Get the domain and version for the app library.
    $this->initializeDomainAndVersion();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'));
  }

  /**
   * Alters the library definition for the Boat Finder React app.
   *
   * This method checks if the 'boat_finder_embed' library exists in the
   * provided library array. If so, it updates the library's JS and CSS URLs
   * based on configuration values for the domain and version of the Boat
   * Finder app.
   *
   * @param array &$libraries
   *   The libraries array.
   * @param string $extension
   *   The module or theme that defines the library.
   */
  public function libraryInfoAlter(array &$libraries, string $extension): void {
    // Check if the library exists in the provided array.
    if (!isset($libraries['boat_finder_embed'])) {
      return;
    }
    // Retrieve the JS and CSS URLs.
    $random = new Random();
    $cache_buster = $random->name(6, TRUE);
    $js_url = $this->getJsUrl() . '?r=' . $cache_buster;
    $css_url = $this->getCssUrl() . '?r=' . $cache_buster;
    // Set the URLs in the 'boat_finder_embed' library.
    $libraries['boat_finder_embed']['js'][$js_url] = [
      'type' => 'external',
      'minified' => TRUE,
      'attributes' => [
        'type' => 'module',
        'crossorigin' => 'anonymous',
      ],
    ];
    $libraries['boat_finder_embed']['css']['component'] = [
      $css_url => [
        'type' => 'external',
        'crossorigin' => 'anonymous',
        'weight' => 100,
      ],
    ];
  }

  /**
   * Changes the group of boat_finder_embed CSS.
   *
   * @param array $css
   *   CSS files.
   *
   * @return void
   */
  public function cssAlter(&$css): void {
    if (!empty($css[$this->getCssUrl()])) {
      $css[$this->getCssUrl()]['group'] = CSS_AGGREGATE_THEME;
    }
  }

  /**
   * Initializes the domain and version properties from configuration.
   */
  private function initializeDomainAndVersion(): void {
    $config = $this->configFactory->get('nmma_boat_finder_embed.settings');
    $this->domain = $config->get('boat_finder_domain') ?: 'https://live-nmma-boat-finder-node.appa.pantheon.site';
    $this->version = $config->get('boat_finder_version') ?: '1.0.0';
  }

  /**
   * Retrieves the JavaScript URL based on the initialized domain and version.
   *
   * @return string
   *   The full URL for the JavaScript file.
   */
  private function getJsUrl(): string {
    return "{$this->domain}/boat-finder-component-{$this->version}.js";
  }

  /**
   * Retrieves the CSS URL based on the initialized domain and version.
   *
   * @return string
   *   The full URL for the CSS file.
   */
  private function getCssUrl(): string {
    return "{$this->domain}/boat-finder-component-{$this->version}.css";
  }

}
