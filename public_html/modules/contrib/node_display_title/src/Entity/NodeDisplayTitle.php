<?php

namespace Drupal\node_display_title\Entity;

use Drupal\node\Entity\Node;

/**
 * Class NodeDisplayTitle. Overrides the base class for node entity type.
 */
class NodeDisplayTitle extends Node {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    if (!\Drupal::service('router.admin_context')->isAdminRoute() &&
      $title = $this->get('display_title')->getString()
    ) {
      return $title;
    }

    return parent::getTitle();
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    if (!\Drupal::service('router.admin_context')->isAdminRoute() &&
      $title = $this->get('display_title')->getString()
    ) {
      return $title;
    }

    return parent::label();
  }

}
