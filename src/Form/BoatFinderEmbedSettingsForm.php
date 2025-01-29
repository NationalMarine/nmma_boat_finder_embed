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
      '#default_value' => $config->get('boat_finder_domain') ?: 'https://live-boatfinderreactapp.appa.pantheon.site',
      '#required' => TRUE,
    ];
    $form['boat_finder_version'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Boat Finder App Version'),
      '#description' => $this->t('Enter the version of the Boat Finder JavaScript and CSS files.'),
      '#default_value' => $config->get('boat_finder_version') ?: '1.0.0',
      '#required' => TRUE,
    ];
    $form['show_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set show ID'),
      '#description' => $this->t('Show boats from the specified show ID.'),
      '#default_value' => $config->get('show_id') ?: 'dbcom',
      '#required' => TRUE,
    ];
    $form['infinite_scroll'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable infinite scrolling'),
      '#description' => $this->t('When checked, the application will load more boats as the user scrolls down the page. When false, a Load More button will be displayed at the bottom of the page.'),
      '#default_value' => $config->get('infinite_scroll') ?: FALSE,
    ];
    $form['max_length'] = [
      '#type' => 'number',
      '#min' => 1,
      '#size' => 1,
      '#title' => $this->t('Set maximum boat length'),
      '#description' => $this->t('Boats that exceed this length (in feet) will not be included in results. Default: 260.'),
      '#default_value' => $config->get('max_length') ?: '260',
    ];
    $form['max_price'] = [
      '#type' => 'number',
      '#min' => 1,
      '#size' => 1,
      '#title' => $this->t('Set maximum boat price'),
      '#description' => $this->t('Boats that exceed this price will not be included in results.'),
      '#default_value' => $config->get('max_price') ?: '1600000',
    ];
    $form['boat_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set boat type'),
      '#description' => $this->t('When provided, restricts the application to only show boats of the specified type.'),
      '#default_value' => $config->get('boat_type') ?: '',
    ];
    $form['boat_brand'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set boat brand'),
      '#description' => $this->t('When provided, restricts the application to only show boats of the specified brand.'),
      '#default_value' => $config->get('boat_brand') ?: '',
    ];
    $form['show_color'] = [
      '#type' => 'color',
      '#title' => $this->t('Set show color'),
      '#description' => [
        '#type' => 'inline_template',
        '#template' => 'Provided as a hex color value (e.g. #adadad). When provided, it will set the color/accent of the following elements: <ul>
          <li>filter</li>
          <li>boat price</li>
          <li>social icons</li>
          <li>description</li>
          <li>booth location</li>
          <li>filter fill color</li>
          <li>reset text color</li>
        </ul>',
      ],
      '#default_value' => $config->get('show_color') ?: '#adadad',
    ];
    $form['modal_sponsor_tagline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set sponsor tag line'),
      '#description' => $this->t("Set the sponsor tag line. Default: Get on Board with America's #1 Boat Insurer"),
      '#default_value' => $config->get('modal_sponsor_tagline') ?: '',
    ];
    $form['modal_sponsor_tagline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set the sponsor tagline'),
      '#description' => $this->t("Set the sponsor image. Default: Get on Board with America's #1 Boat Insurer"),
      '#default_value' => $config->get('modal_sponsor_tagline') ?: '',
    ];
    $form['modal_sponsor_image'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set the sponsor image'),
      '#description' => $this->t("Set the sponsor image. Default: ") . $this->defaultLinks('Sponsor image', 'https://cdn.prod.website-files.com/61d73719ecbb6d7c722e61cb/6526ccc2a52bc0e9c7dddffe_progressive.svg'),
      '#default_value' => $config->get('modal_sponsor_image') ?: '',
    ];
    $form['modal_sponsor_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set the sponsor link'),
      '#description' => $this->t("Set the sponsor link. Default: ") . $this->defaultLinks('Sponsor link', 'https://www.discoverboating.com/progressive-exclusive-insurance-partner'),
      '#default_value' => $config->get('modal_sponsor_link') ?: '',
    ];
    $form['rows_between_sponsor_cards'] = [
      '#type' => 'number',
      '#min' => 1,
      '#size' => 1,
      '#title' => $this->t('Set rows between sponsor cards'),
      '#description' => $this->t('Set the number of rows between sponsor cards.'),
      '#default_value' => $config->get('rows_between_sponsor_cards') ?: '10',
    ];
    $form['sponsor_card_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set the sponsor card link'),
      '#description' => $this->t("Set the sponsor card link. Default: ") . $this->defaultLinks('Sponsor card link', 'https://www.progressive.com/lp/boat-nmma/?code=6739700004'),
      '#default_value' => $config->get('sponsor_card_link') ?: '',
    ];
    $form['sponsor_card_image'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set the sponsor card image'),
      '#description' => $this->t("Set the sponsor card image. Default: ") . $this->defaultLinks('Sponsor card image', 'https://cdn.prod.website-files.com/61d73719ecbb6d7c722e61cb/657a3f685428828d55e8813b_Flo-ad-progressive-boat-finder.jpg'),
      '#default_value' => $config->get('sponsor_card_image') ?: '',
    ];
    $form['city_location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Set analytics cityLocation'),
      '#description' => $this->t("Push cityLocation value to <code>dataLayer</code> event."),
      '#default_value' => $config->get('city_location') ?: '',
    ];
    $form['show_booth_info'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable showing booth information'),
      '#description' => $this->t('When checked, will show the show booth card info.'),
      '#default_value' => $config->get('show_booth_info') ?: FALSE,
    ];
    $form['show_exhibitor_info'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable exhibitor booth information'),
      '#description' => $this->t('When checked, will show the show booth exhibitor info.'),
      '#default_value' => $config->get('show_exhibitor_info') ?: FALSE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Returns a string containing a link to a default value.
   *
   * @param string $text
   *   The text to be displayed in the link.
   * @param string $link
   *   The link URL.
   *
   * @return string
   *   The link as a string.
   */
  private function defaultLinks(string $text, string $link): string {
    $url = Url::fromUri($link);
    $link = Link::fromTextAndUrl($text, $url);
    return $link->toString();
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
    $all_values = $form_state->cleanValues()->getValues();
    $this->configFactory->getEditable('nmma_boat_finder_embed.settings')
      ->set('boat_finder_domain', $all_values['boat_finder_domain'])
      ->set('boat_finder_version', $all_values['boat_finder_version'])
      ->set('show_id', $all_values['show_id'])
      ->set('infinite_scroll', $all_values['infinite_scroll'])
      ->set('max_length', $all_values['max_length'])
      ->set('max_price', $all_values['max_price'])
      ->set('boat_type', $all_values['boat_type'])
      ->set('boat_brand', $all_values['boat_brand'])
      ->set('show_color', $all_values['show_color'])
      ->set('modal_sponsor_tagline', $all_values['modal_sponsor_tagline'])
      ->set('modal_sponsor_image', $all_values['modal_sponsor_image'])
      ->set('modal_sponsor_link', $all_values['modal_sponsor_link'])
      ->set('rows_between_sponsor_cards', $all_values['rows_between_sponsor_cards'])
      ->set('sponsor_card_link', $all_values['sponsor_card_link'])
      ->set('sponsor_card_image', $all_values['sponsor_card_image'])
      ->set('city_location', $all_values['city_location'])
      ->set('show_booth_info', $all_values['show_booth_info'])
      ->set('show_exhibitor_info', $all_values['show_exhibitor_info'])
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

    // Construct the URL for the Boat Finder page.
    $route_url = Url::fromRoute('nmma_boat_finder_embed.find_boats_by_brand');
    $boad_finder_url = \Drupal::request()->getSchemeAndHttpHost() . $route_url->toString();
    // Build the markup for the library links.
    return [
      '#type' => 'markup',
      '#markup' => $this->t('<h6>Boat Finder Page:</h6><p> @boad_finder_url </p><h6>Configured Library:</h6><ol><li><b>JavaScript:</b> @js_link</li><li><b>CSS:</b> @css_link</li></ol>', [
        '@js_link' => Link::fromTextAndUrl($js_url, Url::fromUri($js_url))->toString(),
        '@css_link' => Link::fromTextAndUrl($css_url, Url::fromUri($css_url))->toString(),
        '@boad_finder_url' => Link::fromTextAndUrl($boad_finder_url, Url::fromUri($boad_finder_url))->toString(),
      ]),
    ];
  }

}
