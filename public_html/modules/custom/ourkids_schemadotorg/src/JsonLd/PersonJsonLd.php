<?php

namespace Drupal\ourkids_schemadotorg\JsonLd;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\node\NodeInterface;

/**
 * Builds Person JSON-LD alterations for person nodes.
 */
class PersonJsonLd {

  public static function alter(array &$data, EntityInterface $entity, BubbleableMetadata $bubbleable_metadata): void {
    if (!($entity instanceof NodeInterface)) {
      return;
    }

    // Strip HTML tags from description — body field renders HTML markup
    // which must not appear in JSON-LD plain text properties.
    // Replace closing/opening paragraph tags with double newlines first
    // so paragraph breaks are preserved as whitespace.
    if (!empty($data['description']) && is_string($data['description'])) {
      $text = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n\n", $data['description']);
      $data['description'] = trim(strip_tags($text));
    }

    // Add sameAs from schema_same_as (multi-value link field).
    if ($entity->hasField('schema_same_as') && !$entity->get('schema_same_as')->isEmpty()) {
      $same_as = [];
      foreach ($entity->get('schema_same_as') as $item) {
        if (!empty($item->uri)) {
          $same_as[] = $item->uri;
        }
      }
      if ($same_as) {
        $data['sameAs'] = count($same_as) === 1 ? $same_as[0] : $same_as;
      }
    }
  }

}
