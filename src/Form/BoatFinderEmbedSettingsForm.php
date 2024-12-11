<?php

namespace Drupal\nmma_boat_finder_embed\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

/**
 * Configuration form for Boat Finder React app settings.
 */
class BoatFinderEmbedSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['nmma_boat_finder_embed.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'nmma_boat_finder_embed_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('nmma_boat_finder_embed.settings');
    $form['library_links'] = $this->getLibraryLinks();
    $form['boat_finder_domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Boat Finder App Domain'),
      '#description' => $this->t('Enter the domain where the Boat Finder JavaScript and CSS files are hosted.'),
      '#default_value' => $config->get('boat_finder_domain') ?: 'https://live-nmma-boat-finder-node.appa.pantheon.site',
      '#required' => TRUE,
    ];
    $form['boat_finder_version'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Boat Finder App Version'),
      '#description' => $this->t('Enter the version of the Boat Finder JavaScript and CSS files.'),
      '#default_value' => $config->get('boat_finder_version') ?: '1.0.0',
      '#required' => TRUE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);
    // Get the domain and version input values.
    $domain = $form_state->getValue('boat_finder_domain');
    $version = $form_state->getValue('boat_finder_version');
    // Construct the URLs for the JS and CSS files.
    $js_url = "{$domain}/boat-finder-component-{$version}.js";
    $css_url = "{$domain}/boat-finder-component-{$version}.css";
    // Validate if the JS file is reachable and has the correct MIME type.
    if (!$this->libraryUrlIsReachable($js_url, 'application/javascript')) {
      $url = Url::fromUri($js_url);
      $link = Link::fromTextAndUrl($js_url, $url);
      $form_state->setErrorByName('boat_finder_domain', $this->t('The JavaScript file @link is not reachable or has an invalid MIME type.', ['@link' => $link->toString()]));
    }
    // Validate if the CSS file is reachable and has the correct MIME type.
    if (!$this->libraryUrlIsReachable($css_url, 'text/css')) {
      $url = Url::fromUri($css_url);
      $link = Link::fromTextAndUrl($css_url, $url);
      $form_state->setErrorByName('boat_finder_domain', $this->t('The CSS file @link is not reachable or has an invalid MIME type.', ['@link' => $link->toString()]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('nmma_boat_finder_embed.settings')
      ->set('boat_finder_domain', $form_state->getValue('boat_finder_domain'))
      ->set('boat_finder_version', $form_state->getValue('boat_finder_version'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Helper function to check if a URL is reachable and validate its MIME type.
   *
   * @param string $url
   *   The URL to check.
   * @param string $expected_mime_type
   *   The expected MIME type of the response.
   *
   * @return bool
   *   TRUE if the URL is reachable and has the expected MIME type, FALSE
   *   otherwise.
   */
  private function libraryUrlIsReachable(string $url, string $expected_mime_type): bool {
    try {
      // Use Drupal's httpClient to make a HEAD request with a timeout.
      $response = \Drupal::httpClient()->head($url, ['timeout' => 40]);
      // Get the MIME type from the Content-Type header.
      $content_type = $response->getHeaderLine('Content-Type');
      // Validate the MIME type from the Content-Type header.
      return str_contains($content_type, $expected_mime_type);
    }
    catch (RequestException | ConnectException $e) {
      // Catch exceptions, such as if the URL is unreachable or times out.
      return FALSE;
    }
  }

  /**
   * Builds the library links for the configured JavaScript and CSS files.
   *
   * @return array
   *   A render array for the library links.
   */
  private function getLibraryLinks(): array {
    $config = $this->config('nmma_boat_finder_embed.settings');
    // Get the current domain and version to construct the URLs.
    $domain = $config->get('boat_finder_domain') ?: '';
    $version = $config->get('boat_finder_version') ?: '';
    if (empty($domain) || empty($version)) {
      return [];
    }
    // Construct the URLs for the JS and CSS files.
    $js_url = "{$domain}/boat-finder-component-{$version}.js";
    $css_url = "{$domain}/boat-finder-component-{$version}.css";

    // Build the markup for the library links.
    return [
      '#type' => 'markup',
      '#markup' => $this->t('<h6>Configured Library:</h6><ol><li><b>JavaScript:</b> @js_link</li><li><b>CSS:</b> @css_link</li></ol>', [
        '@js_link' => Link::fromTextAndUrl($js_url, Url::fromUri($js_url))->toString(),
        '@css_link' => Link::fromTextAndUrl($css_url, Url::fromUri($css_url))->toString(),
      ]),
    ];
  }

}
