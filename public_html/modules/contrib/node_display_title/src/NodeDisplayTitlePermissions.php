<?php

namespace Drupal\node_display_title;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\NodeType;

/**
 * Provides dynamic permissions for node display title module.
 */
class NodeDisplayTitlePermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of node display title permissions.
   *
   * @return array
   *   An array of permissions for node types.
   */
  public function permissions() {
    $permissions = [];

    foreach (NodeType::loadMultiple() as $node_type) {
      $permissions["access {$node_type->id()} display title field"] = [
        'title' => $this->t('Access @node_type display title field', ['@node_type' => $node_type->label()]),
      ];
    }

    return $permissions;
  }

}
