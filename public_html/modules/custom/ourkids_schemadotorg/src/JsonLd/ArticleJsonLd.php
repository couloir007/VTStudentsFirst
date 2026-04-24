<?php

namespace Drupal\ourkids_schemadotorg\JsonLd;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\node\NodeInterface;

/**
 * Builds Article JSON-LD alterations for article nodes.
 */
class ArticleJsonLd {

  public static function alter(array &$data, EntityInterface $entity, BubbleableMetadata $bubbleable_metadata): void {
    if (!($entity instanceof NodeInterface)) {
      return;
    }

    // Strip HTML tags from description.
    if (!empty($data['description']) && is_string($data['description'])) {
      $data['description'] = trim(strip_tags($data['description']));
    }

    // Add articleBody as plain text from body field.
    if ($entity->hasField('body') && !$entity->get('body')->isEmpty()) {
      $data['articleBody'] = trim(strip_tags($entity->get('body')->processed));
    }
  }

}
