<?php

namespace Drupal\nmma_boat_finder_embed\Plugin\Filter;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Embeds the Boat Finder App block into content.
 *
 * @Filter(
 *   id = "embed_boat_finder_app_block",
 *   title = @Translation("Embed Boat Finder App Block"),
 *   description = @Translation("Allows embedding of Boat Finder App blocks
 *   into content."), type =
 *   Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class BoatFinderAppBlockFilter extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The block plugin manager service.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $blockPluginManager;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The block plugin ID.
   *
   * @var string
   */
  protected $blockPluginId = 'boat_finder_embed_block';

  /**
   * The shortcode pattern.
   *
   * @var string
   */
  protected $shortcodePattern = '/\[boat_finder_app\]/';

  /**
   * Creates a new filter class instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Block\BlockManagerInterface $block_plugin_manager
   *   The block plugin manager service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BlockManagerInterface $block_plugin_manager, RendererInterface $renderer, AccountInterface $current_user, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->blockPluginManager = $block_plugin_manager;
    $this->renderer = $renderer;
    $this->currentUser = $current_user;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.block'),
      $container->get('renderer'),
      $container->get('current_user'),
      // Ensure this service is defined in services.yml.
      $container->get('logger.channel.nmma_boat_finder_embed')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $response = new FilterProcessResult();
    // Find all occurrences of the shortcode in the text.
    preg_match_all($this->shortcodePattern, $text, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
      $text = $this->processEmbed($text, $match, $response);
    }
    return $response->setProcessedText($text);
  }

  /**
   * Processes a single shortcode embed and replaces it with the block content.
   *
   * @param string $text
   *   The text to be processed.
   * @param array $match
   *   The match found in the text.
   * @param \Drupal\filter\FilterProcessResult $response
   *   The filter process result object.
   *
   * @return string
   *   The processed text.
   */
  protected function processEmbed($text, array $match, FilterProcessResult $response): string {
    try {
      $block_plugin = $this->blockPluginManager->createInstance($this->blockPluginId);
      $block_content = $this->getBlockContent($block_plugin);
      $text = str_replace($match[0], $block_content, $text);
      $response->addCacheableDependency($block_plugin);
    }
    catch (PluginException $exception) {
      // Log the exception if needed.
      $this->logger->error('Error processing embed: @message', ['@message' => $exception->getMessage()]);
    }
    return $text;
  }

  /**
   * Retrieves the block content to be embedded.
   *
   * @param \Drupal\Core\Block\BlockBase $block_plugin
   *   The block plugin instance.
   *
   * @return string
   *   The rendered block content.
   */
  protected function getBlockContent($block_plugin): string {
    if ($block_plugin->access($this->currentUser)) {
      $build = $block_plugin->build();
      return $this->renderer->render($build);
    }
    return '';
  }

}
