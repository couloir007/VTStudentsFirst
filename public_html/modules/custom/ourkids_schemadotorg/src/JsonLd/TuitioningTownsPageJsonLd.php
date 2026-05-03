<?php

namespace Drupal\ourkids_schemadotorg\JsonLd;

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\node\NodeInterface;

/**
 * Builds ItemList JSON-LD for the Vermont Tuitioning Towns page.
 *
 * Adds a mainEntity ItemList of Place items to the WebPage JSON-LD.
 * Place nodes are published but access-denied via Rabbit Hole, so they
 * are available to entity queries but not directly browseable.
 *
 * The ItemList pattern:
 *
 * "mainEntity": {
 *   "@type": "ItemList",
 *   "name": "Vermont Tuitioning Communities",
 *   "numberOfItems": 95,
 *   "itemListElement": [
 *     {
 *       "@type": "ListItem",
 *       "position": 1,
 *       "item": {
 *         "@type": "Place",
 *         "name": "Alburgh",
 *         "url": "https://ourkidsourschoolsvt.org/node/123"
 *       }
 *     },
 *     ...
 *   ]
 * }
 */
class TuitioningTownsPageJsonLd {

  /**
   * The node ID of the Vermont Tuitioning Towns page.
   */
  const PAGE_NID = 349;

  /**
   * Alters the WebPage JSON-LD data for the tuitioning towns listing page.
   */
  public static function alter(array &$data, NodeInterface $node, BubbleableMetadata $bubbleable_metadata): void {
    if ((int) $node->id() !== self::PAGE_NID) {
      return;
    }

    $node_storage = \Drupal::entityTypeManager()->getStorage('node');

    // Load all published Place nodes, sorted by title.
    $nids = $node_storage->getQuery()
      ->condition('type', 'place')
      ->condition('status', 1)
      ->sort('title', 'ASC')
      ->accessCheck(FALSE)
      ->execute();

    if (empty($nids)) {
      return;
    }

    $places = $node_storage->loadMultiple($nids);

    $items = [];
    $position = 1;

    foreach ($places as $place) {
      $item = [
        '@type' => 'Place',
        'name'  => $place->label(),
        'url'   => $place->toUrl('canonical', ['absolute' => TRUE])->toString(),
      ];

      $lat = $place->get('schema_latitude')->value ?? NULL;
      $lon = $place->get('schema_longitude')->value ?? NULL;

      if ($lat !== NULL && $lon !== NULL) {
        $item['geo'] = [
          '@type'     => 'GeoCoordinates',
          'latitude'  => (float) $lat,
          'longitude' => (float) $lon,
        ];
      }

      $items[] = [
        '@type'    => 'ListItem',
        'position' => $position++,
        'item'     => $item,
      ];

      // Add each Place node as a cache dependency.
      $bubbleable_metadata->addCacheableDependency($place);
    }

    // Attach to mainEntity on the WebPage wrapper.
    // SchemaBlueprints may have already set mainEntity from schema_main_entity;
    // override it here since this page has no paragraph mainEntity.
    $data['schemadotorg_jsonld_entity']['mainEntity'] = [
      '@type'           => 'ItemList',
      'name'            => 'Vermont Tuitioning Communities',
      'numberOfItems'   => count($items),
      'itemListElement' => $items,
    ];

    // Cache tag so the JSON-LD invalidates when any Place node changes.
    $bubbleable_metadata->addCacheTags(['node_list:place']);
  }

}
