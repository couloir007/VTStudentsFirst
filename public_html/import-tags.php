<?php

/**
 * @file import-tags.php
 *
 * Imports keyword topics as taxonomy terms into the 'tags' vocabulary
 * and applies them to the matching article nodes.
 *
 * Usage:
 *   drush php-script import-tags.php
 *
 * Run from the Drupal root or with lando:
 *   lando drush php-script public_html/import-tags.php
 */

// Tags per article — keyed by node URL alias (path).
// Update the paths if your URL aliases differ.
$article_tags = [
  'news/act-73-vermont-explained-nek-families' => [
    'Act 73 Vermont',
    'Vermont school choice',
    'Vermont tuitioning',
    'Vermont independent schools',
    'Vermont education reform 2025',
    'Northeast Kingdom education',
    'Vermont school consolidation',
    'Vermont tuitioning restrictions',
  ],
  'news/h777-h813-vermont-independent-schools' => [
    'H.777 Vermont',
    'H.813 Vermont',
    'Vermont independent schools',
    'Vermont school choice legislation',
    'Vermont education bills 2026',
    'Act 73 Vermont',
    'Vermont tuitioning accountability',
    'Vermont school choice restrictions',
  ],
  'news/st-johnsbury-academy-lyndon-institute-nek' => [
    'St. Johnsbury Academy',
    'Lyndon Institute Vermont',
    'Northeast Kingdom schools',
    'Vermont tuitioning',
    'Vermont school choice',
    'Act 73 Vermont',
    'Vermont independent schools',
    'NEK economy education',
    'rural Vermont workforce',
  ],
  'news/nek-builds-its-own' => [
    'Northeast Kingdom Vermont',
    'Kingdom Trails East Burke',
    'Vermont school choice history',
    'St. Johnsbury Academy history',
    'Lyndon Institute Vermont',
    'Vermont Yankee ingenuity',
    'rural Vermont economy',
    'Act 73 Vermont',
    'Vermont tuitioning tradition',
    'East Burke Vermont economy',
  ],
  'news/vermont-tuitioning-history-origins-and-four-historic-academies' => [
    'Vermont tuitioning history',
    'Vermont school choice 1869',
    'St. Johnsbury Academy history',
    'Lyndon Institute history',
    'Thetford Academy Vermont',
    'Burr and Burton Academy Vermont',
    'Vermont independent schools history',
    'Vermont public tuition',
    'Act 73 Vermont',
    'Vermont tuitioning towns',
  ],
  'news/vermont-town-tuition-program-explained' => [
    'Vermont town tuition program',
    'Vermont tuitioning explained',
    'Vermont school choice',
    'Act 73 Vermont',
    'Northeast Kingdom schools',
    'Riverside School Vermont',
    'Vermont independent schools',
    'Vermont rural education',
    'Vermont tuitioning towns',
    'Vermont public education rural',
  ],
  'news/what-st-johnsbury-academy-actually' => [
    'St. Johnsbury Academy',
    'Vermont tuitioning',
    'Vermont school choice facts',
    'Vermont independent schools',
    'Vermont special education tuitioning',
    'per-pupil spending Vermont',
    'Act 73 Vermont',
    'Vermont school choice myths',
    'Vermont public tuition costs',
    'SJA Vermont admissions',
  ],
  'news/why-kingdom-east-families-should-care-about-every-tuitioning-town-vermont' => [
    'Kingdom East School District',
    'Vermont tuitioning',
    'St. Johnsbury Academy tuitioning',
    'Lyndon Institute tuitioning',
    'East Burke School Vermont',
    'Act 73 Vermont',
    'Vermont school choice local control',
    'Northeast Kingdom education',
    'Vermont education reform 2026',
    'Vermont tuitioning towns local control',
  ],
];

$term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
$node_storage = \Drupal::entityTypeManager()->getStorage('node');
$alias_manager = \Drupal::service('path_alias.manager');

$vocab = 'tags'; // Change to your vocabulary machine name if different
$term_cache = [];
$total_nodes = 0;
$total_tags = 0;

echo "\n=== OurKidsOurSchoolsVT Tag Import ===\n\n";

foreach ($article_tags as $path => $tags) {

  // Resolve path alias to node ID.
  $system_path = $alias_manager->getPathByAlias('/' . $path);
  if (!preg_match('/node\/(\d+)/', $system_path, $matches)) {
    echo "⚠️  Could not find node for path: /{$path}\n";
    continue;
  }

  $nid = $matches[1];
  $node = $node_storage->load($nid);

  if (!$node) {
    echo "⚠️  Node {$nid} not found for path: /{$path}\n";
    continue;
  }

  echo "📄 {$node->label()} (node/{$nid})\n";

  // Get existing tag IDs to avoid duplicates.
  $existing_tids = [];
  if ($node->hasField('field_tags')) {
    foreach ($node->get('field_tags')->referencedEntities() as $existing_term) {
      $existing_tids[$existing_term->id()] = TRUE;
    }
  }

  $new_tids = [];

  foreach ($tags as $tag_name) {

    // Check cache first.
    if (isset($term_cache[$tag_name])) {
      $tid = $term_cache[$tag_name];
    }
    else {
      // Look for existing term.
      $existing = $term_storage->loadByProperties([
        'name' => $tag_name,
        'vid'  => $vocab,
      ]);

      if ($existing) {
        $term = reset($existing);
        $tid = $term->id();
        echo "  ✓ Existing tag: {$tag_name}\n";
      }
      else {
        // Create new term.
        $term = $term_storage->create([
          'name' => $tag_name,
          'vid'  => $vocab,
        ]);
        $term->save();
        $tid = $term->id();
        echo "  + Created tag: {$tag_name}\n";
        $total_tags++;
      }

      $term_cache[$tag_name] = $tid;
    }

    if (!isset($existing_tids[$tid])) {
      $new_tids[] = ['target_id' => $tid];
      $existing_tids[$tid] = TRUE;
    }
  }

  // Append new tags to existing ones.
  if ($new_tids) {
    $current = $node->get('field_tags')->getValue();
    $node->set('field_tags', array_merge($current, $new_tids));
    $node->save();
    echo "  → Saved " . count($new_tids) . " new tag(s)\n";
  }
  else {
    echo "  → No new tags to add\n";
  }

  $total_nodes++;
  echo "\n";
}

echo "=== Complete ===\n";
echo "Nodes updated: {$total_nodes}\n";
echo "New terms created: {$total_tags}\n\n";
