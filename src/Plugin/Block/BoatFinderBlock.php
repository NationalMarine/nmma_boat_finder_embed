<?php

namespace Drupal\nmma_boat_finder_embed\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BoatFinderBlock' block.
 *
 * @Block(
 *  id = "boat_finder_block",
 *  admin_label = @Translation("Boat Finder Block"),
 * )
 */
class BoatFinderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = ['#theme' => 'nmma_boat_finder'];

    $build['#attached']['library'][] = 'nmma_boat_finder/boatfinder';

    return $build;
  }

}
