<?php

namespace Drupal\ourkids_schemadotorg\JsonLd;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\node\NodeInterface;

/**
 * Builds Profile JSON-LD alterations for person nodes.
 */
class ProfilePageJsonLd {

  public static function alter(array &$data, EntityInterface $entity, BubbleableMetadata $bubbleable_metadata): void {
    if (!($entity instanceof NodeInterface)) {
      return;
    }

    // Strip Markdown link syntax from copyrightHolder.
    // The organization name field renders as a Markdown link e.g.
    // [OurKidsOurSchoolsVT.org](http://OurKidsOurSchoolsVT.org)
    if (!empty($data['copyrightHolder'])) {
      unset($data['copyrightHolder']);
    }
  }
}

