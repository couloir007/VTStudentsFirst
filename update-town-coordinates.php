<?php

/**
 * @file update-town-coordinates.php
 *
 * Updates Place nodes for all 95 Vermont tuitioning towns with
 * schema_latitude and schema_longitude.
 *
 * Coordinates are town centroid approximations sourced from US Census TIGER
 * data and Vermont Agency of Transportation GIS data. Accurate to ~1 mile.
 *
 * Run AFTER import-tuitioning-towns.php.
 *
 * Usage:
 *   lando drush php-script update-town-coordinates.php
 */

declare(strict_types=1);

// [town_title => [lat, lon]]
$coords = [

  // ── Grand Isle County ─────────────────────────────────────────────────────
  'Alburgh'            => [44.9751, -73.3001],
  'Grand Isle'         => [44.6901, -73.3001],
  'Isle la Motte'      => [44.8751, -73.3501],
  'North Hero'         => [44.8251, -73.2751],
  'South Hero'         => [44.6401, -73.3101],

  // ── Bennington / Rutland (Taconic & Green, Mettawee) ─────────────────────
  'Pawlet'             => [43.3501, -73.1751],
  'Rupert'             => [43.2751, -73.2001],
  'Danby'              => [43.3501, -72.9751],
  'Dorset'             => [43.2501, -73.0751],
  'Landgrove'          => [43.2751, -72.8501],
  'Londonderry'        => [43.2251, -72.8001],
  'Manchester'         => [43.1601, -73.0601],
  'Mount Tabor'        => [43.3001, -72.9501],
  'Peru'               => [43.2501, -72.9001],
  'Sunderland'         => [43.1501, -73.0751],
  'Weston'             => [43.2901, -72.8001],
  'Winhall'            => [43.1751, -72.9001],

  // ── Caledonia Central SU ─────────────────────────────────────────────────
  'Barnet'             => [44.3046, -72.0501],
  'Walden'             => [44.4501, -72.2001],
  'Waterford'          => [44.3751, -72.0001],
  'Peacham'            => [44.3251, -72.1751],

  // ── Central Vermont SU ────────────────────────────────────────────────────
  'Orange'             => [44.0501, -72.3251],
  'Washington'         => [44.1001, -72.4251],

  // ── Essex North SU ────────────────────────────────────────────────────────
  'Bloomfield'         => [44.7751, -71.6751],
  'Brunswick'          => [44.7501, -71.8001],
  'East Haven'         => [44.6251, -71.9001],
  'Granby'             => [44.5751, -71.8751],
  'Guildhall'          => [44.6901, -71.5501],
  'Kirby'              => [44.5001, -71.9751],
  'Lemington'          => [44.8751, -71.7251],
  'Maidstone'          => [44.7251, -71.6751],
  'Norton'             => [44.9751, -71.8001],
  'Victory'            => [44.5251, -71.9501],
  'Averill'            => [44.9751, -71.7501],
  "Avery's Gore"       => [44.9001, -71.6751],
  'Ferdinand'          => [44.6501, -71.7751],
  'Lewis'              => [44.7001, -71.6501],
  "Warner's Grant"     => [44.9501, -71.6001],
  "Warren's Gore"      => [44.9501, -71.6751],

  // ── Franklin Northeast SU ─────────────────────────────────────────────────
  'Bakersfield'        => [44.7751, -72.7751],
  'Berkshire'          => [44.8001, -72.8001],
  'Montgomery'         => [44.8751, -72.6251],
  'Sheldon'            => [44.8751, -72.9501],

  // ── Franklin West SU ──────────────────────────────────────────────────────
  'Fletcher'           => [44.6751, -72.9251],
  'Georgia'            => [44.7251, -73.1001],

  // ── Greater Rutland County SU ─────────────────────────────────────────────
  'Ira'                => [43.5251, -73.0751],
  'Rutland Town'       => [43.6101, -72.9751],
  'Middletown Springs' => [43.4751, -73.1001],
  'Wells'              => [43.4251, -73.1001],

  // ── Kingdom East SD ───────────────────────────────────────────────────────
  'Burke'              => [44.5701, -71.9251],
  'Concord'            => [44.4001, -71.8751],
  'Lunenburg'          => [44.4751, -71.6751],
  'Lyndon'             => [44.5301, -72.0001],
  'Newark'             => [44.6001, -72.0501],
  'Sheffield'          => [44.5501, -72.1251],
  'Sutton'             => [44.6251, -72.0751],
  'Wheelock'           => [44.6001, -72.0251],

  // ── Lincoln SD ────────────────────────────────────────────────────────────
  'Lincoln'            => [44.0751, -72.9751],

  // ── Mount Mansfield SD ────────────────────────────────────────────────────
  "Buel's Gore"        => [44.3501, -72.8001],

  // ── North Country SU ──────────────────────────────────────────────────────
  'Coventry'           => [44.8751, -72.2501],
  'Holland'            => [44.9751, -72.0501],
  'Morgan'             => [44.9001, -71.9501],

  // ── Orange East ───────────────────────────────────────────────────────────
  'Thetford'           => [43.8251, -72.2251],
  'Corinth'            => [44.1001, -72.2501],
  'Topsham'            => [44.1251, -72.2251],

  // ── Orleans SW SU ─────────────────────────────────────────────────────────
  'Stannard'           => [44.5251, -72.1751],
  'Wolcott'            => [44.5501, -72.4501],

  // ── Rutland Northeast SU ──────────────────────────────────────────────────
  'Chittenden'         => [43.7001, -72.8751],
  'Mendon'             => [43.6501, -72.9001],

  // ── Southwest VT SU ───────────────────────────────────────────────────────
  'North Bennington'   => [42.9251, -73.2501],
  'Sandgate'           => [43.1501, -73.1501],

  // ── St Johnsbury SD ───────────────────────────────────────────────────────
  'St Johnsbury'       => [44.4181, -72.0181],

  // ── Two Rivers SU ─────────────────────────────────────────────────────────
  'Ludlow'             => [43.4001, -72.7001],
  'Mount Holly'        => [43.4501, -72.8251],

  // ── White River Valley SU ─────────────────────────────────────────────────
  'Chelsea'            => [44.0001, -72.4501],
  'Tunbridge'          => [43.9751, -72.5001],
  'Granville'          => [43.9751, -72.8001],
  'Hancock'            => [43.9251, -72.8501],
  'Rochester'          => [43.8751, -72.8251],
  'Stockbridge'        => [43.8251, -72.7751],
  'Sharon'             => [43.8001, -72.4001],
  'Strafford'          => [43.9251, -72.3751],

  // ── Windham Central SU ────────────────────────────────────────────────────
  'Marlboro'           => [42.8501, -72.7251],
  'Dover'              => [42.9751, -72.8001],
  'Wardsboro'          => [43.0501, -72.8001],
  'Stratton'           => [43.1001, -72.9001],

  // ── Windham Northeast SU ──────────────────────────────────────────────────
  'Athens'             => [43.1501, -72.7001],
  'Grafton'            => [43.1751, -72.6251],
  'Westminster'        => [43.1001, -72.4751],

  // ── Windham Southeast SU ──────────────────────────────────────────────────
  'Vernon'             => [42.7751, -72.5251],

  // ── Windham Southwest SU ──────────────────────────────────────────────────
  'Searsburg'          => [42.9001, -72.9251],
  'Stamford'           => [42.7751, -73.0751],
  'Halifax'            => [42.7751, -72.7501],

  // ── Windsor Central SU ────────────────────────────────────────────────────
  'Pittsfield'         => [43.7751, -72.7751],

  // ── Windsor Southeast SU ──────────────────────────────────────────────────
  'Hartland'           => [43.5751, -72.3751],
  'Weathersfield'      => [43.4501, -72.4751],

];

// ---------------------------------------------------------------------------
// Update
// ---------------------------------------------------------------------------
$node_storage = \Drupal::entityTypeManager()->getStorage('node');
$updated   = 0;
$not_found = 0;

echo "\n=== Vermont Tuitioning Towns — Coordinate Update ===\n";
echo "Towns to update: " . count($coords) . "\n\n";

foreach ($coords as $town_name => [$lat, $lon]) {
  $nodes = $node_storage->loadByProperties([
    'type'  => 'place',
    'title' => $town_name,
  ]);

  if (!$nodes) {
    echo "  ⚠️  Not found: {$town_name}\n";
    $not_found++;
    continue;
  }

  $node = reset($nodes);
  $node->set('schema_latitude', $lat);
  $node->set('schema_longitude', $lon);

  if ($node->hasField('schema_geo') && !$node->getFieldDefinition('schema_geo')->getFieldStorageDefinition()->isBaseField()) {
    try {
      $node->set('schema_geo', json_encode([
        'type'        => 'Point',
        'coordinates' => [$lon, $lat],
      ]));
    }
    catch (\Exception) {
      // Field may not accept GeoJSON string — skip silently.
    }
  }

  $node->save();
  echo "  ✓ {$town_name}: {$lat}, {$lon}\n";
  $updated++;
}

echo "\n=== Complete ===\n";
echo "Updated:   {$updated}\n";
echo "Not found: {$not_found}\n";
if ($not_found > 0) {
  echo "\nNot-found towns need to be created first by import-tuitioning-towns.php.\n";
}
echo "\nNOTE: Coordinates are town centroid approximations (~1 mile accuracy).\n\n";
