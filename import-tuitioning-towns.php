<?php

/**
 * @file import-tuitioning-towns.php
 *
 * Imports Vermont tuitioning towns as Place nodes.
 *
 * Source: Vermont Agency of Education, "Education in Vermont — Supervisory
 * Unions/Districts, Town Operating and Tuitioning Grades" (FY 2024, 3-19-24),
 * filtered to rows containing tuitioning grades only.
 *
 * Total: 95 towns.
 *
 * AOE spreadsheet typos corrected in this script:
 *   - "Brunsick"  → Brunswick
 *   - "Mt Tabor"  → Mount Tabor
 *   - "Peachham"  → Peacham (district name; town title uses correct spelling)
 *   - Strafford grade column reads "K-8 o / 9-12 " (missing trailing 't');
 *     included because it appears in the tuitioning-only filtered sheet.
 *
 * field_grade_levels values:
 *   k_12_t — tuitions all grades (no local operating school)
 *   k_8    — operates K–8, tuitions 9–12
 *   k_6    — operates K–6 (or similar), tuitions 7–12
 *
 * Usage:
 *   lando drush php-script import-tuitioning-towns.php
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Town data — generated directly from AOE FY2024 filtered spreadsheet.
// Columns: [title, su, district, grade_info, field_grade_levels, field_act73_status]
// ---------------------------------------------------------------------------
$towns = [

  // ── Grand Isle County ─────────────────────────────────────────────────────
  ['Alburgh',            'Grand Isle',           'Alburgh School District',                               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Grand Isle',         'Grand Isle',           'Champlain Islands Unified Union School District',        'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Isle la Motte',      'Grand Isle',           'Champlain Islands Unified Union School District',        'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['North Hero',         'Grand Isle',           'Champlain Islands Unified Union School District',        'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['South Hero',         'Grand Isle',           'South Hero School District',                            'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Bennington-Rutland SU ─────────────────────────────────────────────────
  ['Pawlet',             'Bennington-Rutland SU','Mettawee School District',                              'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Rupert',             'Bennington-Rutland SU','Mettawee School District',                              'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Danby',              'Bennington-Rutland SU','Taconic & Green Regional School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Dorset',             'Bennington-Rutland SU','Taconic & Green Regional School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Landgrove',          'Bennington-Rutland SU','Taconic & Green Regional School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Londonderry',        'Bennington-Rutland SU','Taconic & Green Regional School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Manchester',         'Bennington-Rutland SU','Taconic & Green Regional School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Mount Tabor',        'Bennington-Rutland SU','Taconic & Green Regional School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Peru',               'Bennington-Rutland SU','Taconic & Green Regional School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Sunderland',         'Bennington-Rutland SU','Taconic & Green Regional School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Weston',             'Bennington-Rutland SU','Taconic & Green Regional School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Winhall',            'Bennington-Rutland SU','Winhall School District',                               'K-12 t',          'k_12_t', 'yes'],

  // ── Caledonia Central SU ─────────────────────────────────────────────────
  ['Barnet',             'Caledonia Central SU', 'Caledonia Coop UUSD',                                  'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Walden',             'Caledonia Central SU', 'Caledonia Coop UUSD',                                  'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Waterford',          'Caledonia Central SU', 'Caledonia Coop UUSD',                                  'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Peacham',            'Caledonia Central SU', 'Peacham School District',                               'K-6 o / 7-12 t',  'k_6',    'yes'],

  // ── Central Vermont SU ────────────────────────────────────────────────────
  ['Orange',             'Central Vermont SU',   'Echo Valley Community School District',                 'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Washington',         'Central Vermont SU',   'Echo Valley Community School District',                 'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Essex North SU ────────────────────────────────────────────────────────
  // NEK Choice School District
  ['Bloomfield',         'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  ['Brunswick',          'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  ['East Haven',         'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  ['Granby',             'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  ['Guildhall',          'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  ['Kirby',              'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  ['Lemington',          'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  ['Maidstone',          'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  ['Norton',             'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  ['Victory',            'Essex North SU',       'NEK Choice School District',                            'K-12 t',          'k_12_t', 'yes'],
  // Unorganized towns / gores
  ['Averill',            'Essex North SU',       'Averill',                                               'K-12 t',          'k_12_t', 'yes'],
  ["Avery's Gore",       'Essex North SU',       "Avery's Gore",                                          'K-12 t',          'k_12_t', 'yes'],
  ['Ferdinand',          'Essex North SU',       'Ferdinand',                                             'K-12 t',          'k_12_t', 'yes'],
  ['Lewis',              'Essex North SU',       'Lewis',                                                 'K-12 t',          'k_12_t', 'yes'],
  ["Warner's Grant",     'Essex North SU',       "Warner's Grant",                                        'K-12 t',          'k_12_t', 'yes'],
  ["Warren's Gore",      'Essex North SU',       "Warren's Gore",                                         'K-12 t',          'k_12_t', 'yes'],

  // ── Franklin Northeast SU ─────────────────────────────────────────────────
  ['Bakersfield',        'Franklin Northeast SU','Northern Mountain Valley Unified Union School District', 'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Berkshire',          'Franklin Northeast SU','Northern Mountain Valley Unified Union School District', 'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Montgomery',         'Franklin Northeast SU','Northern Mountain Valley Unified Union School District', 'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Sheldon',            'Franklin Northeast SU','Northern Mountain Valley Unified Union School District', 'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Franklin West SU ──────────────────────────────────────────────────────
  ['Fletcher',           'Franklin West SU',     'Fletcher School District',                              'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Georgia',            'Franklin West SU',     'Georgia School District',                               'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Greater Rutland County SU ─────────────────────────────────────────────
  ['Ira',                'Greater Rutland Co SU','Ira School District',                                   'K-12 t',          'k_12_t', 'yes'],
  ['Rutland Town',       'Greater Rutland Co SU','Rutland Town School District',                          'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Middletown Springs', 'Greater Rutland Co SU','Wells Springs Unified Union School District',            'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Wells',              'Greater Rutland Co SU','Wells Springs Unified Union School District',            'K-6 o / 7-12 t',  'k_6',    'yes'],

  // ── Kingdom East SD ───────────────────────────────────────────────────────
  ['Burke',              'Kingdom East SD',      'Kingdom East Unified Union School District',             'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Concord',            'Kingdom East SD',      'Kingdom East Unified Union School District',             'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Lunenburg',          'Kingdom East SD',      'Kingdom East Unified Union School District',             'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Lyndon',             'Kingdom East SD',      'Kingdom East Unified Union School District',             'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Newark',             'Kingdom East SD',      'Kingdom East Unified Union School District',             'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Sheffield',          'Kingdom East SD',      'Kingdom East Unified Union School District',             'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Sutton',             'Kingdom East SD',      'Kingdom East Unified Union School District',             'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Wheelock',           'Kingdom East SD',      'Kingdom East Unified Union School District',             'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Lincoln SD ────────────────────────────────────────────────────────────
  ['Lincoln',            'Lincoln SD',           'Lincoln School District',                               'K-6 o / 7-12 t',  'k_6',    'yes'],

  // ── Mount Mansfield SD ────────────────────────────────────────────────────
  ["Buel's Gore",        'Mount Mansfield SD',   "Buel's Gore",                                           'K-12 t',          'k_12_t', 'yes'],

  // ── North Country SU ──────────────────────────────────────────────────────
  ['Coventry',           'North Country SU',     'Coventry School District',                              'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Holland',            'North Country SU',     'Holland School District',                               'K-12 t',          'k_12_t', 'yes'],
  ['Morgan',             'North Country SU',     'Morgan School District',                                'K-6 t',           'k_12_t', 'yes'],

  // ── Orange East ───────────────────────────────────────────────────────────
  ['Thetford',           'Orange East',          'Thetford School District',                              'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Corinth',            'Orange East',          'Waits River Valley Union School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Topsham',            'Orange East',          'Waits River Valley Union School District',               'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Orleans SW SU ─────────────────────────────────────────────────────────
  ['Stannard',           'Orleans SW SU',        'Stannard School District',                              '7-12 t',          'k_6',    'yes'],
  ['Wolcott',            'Orleans SW SU',        'Wolcott School District',                               'K-6 o / 7-12 t',  'k_6',    'yes'],

  // ── Rutland Northeast SU ──────────────────────────────────────────────────
  ['Chittenden',         'Rutland Northeast SU', 'Barstow Unified Union School District',                 'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Mendon',             'Rutland Northeast SU', 'Barstow Unified Union School District',                 'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Southwest VT SU ───────────────────────────────────────────────────────
  ['North Bennington',   'Southwest VT SU',      'North Bennington ID',                                   'K-6 t',           'k_12_t', 'yes'],
  ['Sandgate',           'Southwest VT SU',      'Sandgate School District',                              'K-12 t',          'k_12_t', 'yes'],

  // ── St Johnsbury SD ───────────────────────────────────────────────────────
  ['St Johnsbury',       'St Johnsbury SD',      'St Johnsbury School District',                          'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Two Rivers SU ─────────────────────────────────────────────────────────
  ['Ludlow',             'Two Rivers SU',        'Ludlow-Mt Holly Unified Union School District',          'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Mount Holly',        'Two Rivers SU',        'Ludlow-Mt Holly Unified Union School District',          'K-6 o / 7-12 t',  'k_6',    'yes'],

  // ── White River Valley SU ─────────────────────────────────────────────────
  ['Chelsea',            'White River Valley SU','First Branch Unified School District',                   'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Tunbridge',          'White River Valley SU','First Branch Unified School District',                   'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Granville',          'White River Valley SU','Granville-Hancock Unified District',                     'K-12 t',          'k_12_t', 'yes'],
  ['Hancock',            'White River Valley SU','Granville-Hancock Unified District',                     'K-12 t',          'k_12_t', 'yes'],
  ['Rochester',          'White River Valley SU','Rochester-Stockbridge Unified District',                 'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Stockbridge',        'White River Valley SU','Rochester-Stockbridge Unified District',                 'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Sharon',             'White River Valley SU','Sharon School District',                                 'K-6 o / 7-12 t',  'k_6',    'yes'],
  // AOE grade column reads "K-8 o / 9-12 " (missing trailing 't') —
  // included because this row appears in the tuitioning-filtered sheet.
  ['Strafford',          'White River Valley SU','Strafford School District',                              'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Windham Central SU ────────────────────────────────────────────────────
  ['Marlboro',           'Windham Central SU',   'Marlboro School District',                              'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Dover',              'Windham Central SU',   'River Valleys Unified School District',                  'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Wardsboro',          'Windham Central SU',   'River Valleys Unified School District',                  'K-6 o / 7-12 t',  'k_6',    'yes'],
  ['Stratton',           'Windham Central SU',   'Stratton School District',                              'K-12 t',          'k_12_t', 'yes'],

  // ── Windham Northeast SU ──────────────────────────────────────────────────
  ['Athens',             'Windham Northeast SU', 'Windham Northeast Union Elementary School District',     'K-6 o / 7-8 t',   'k_6',    'yes'],
  ['Grafton',            'Windham Northeast SU', 'Windham Northeast Union Elementary School District',     'K-6 o / 7-8 t',   'k_6',    'yes'],
  ['Westminster',        'Windham Northeast SU', 'Windham Northeast Union Elementary School District',     'K-6 o / 7-8 t',   'k_6',    'yes'],

  // ── Windham Southeast SU ──────────────────────────────────────────────────
  ['Vernon',             'Windham Southeast SU', 'Vernon School District',                                'K-6 o / 7-12 t',  'k_6',    'yes'],

  // ── Windham Southwest SU ──────────────────────────────────────────────────
  ['Searsburg',          'Windham Southwest SU', 'Searsburg School District',                             'K-12 t',          'k_12_t', 'yes'],
  ['Stamford',           'Windham Southwest SU', 'Stamford School District',                              'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Halifax',            'Windham Southwest SU', 'Halifax',                                               'K-8 o / 9-12 t',  'k_8',    'yes'],

  // ── Windsor Central SU ────────────────────────────────────────────────────
  ['Pittsfield',         'Windsor Central SU',   'Pittsfield School District',                            'K-12 t',          'k_12_t', 'yes'],

  // ── Windsor Southeast SU ──────────────────────────────────────────────────
  ['Hartland',           'Windsor Southeast SU', 'Hartland School District',                              'K-8 o / 9-12 t',  'k_8',    'yes'],
  ['Weathersfield',      'Windsor Southeast SU', 'Weathersfield School District',                         'K-8 o / 9-12 t',  'k_8',    'yes'],

];

// ---------------------------------------------------------------------------
// Import
// ---------------------------------------------------------------------------
$node_storage = \Drupal::entityTypeManager()->getStorage('node');
$created  = 0;
$updated  = 0;
$errors   = 0;

echo "\n=== Vermont Tuitioning Towns — Import ===\n";
echo "Towns to import: " . count($towns) . "\n\n";

foreach ($towns as [$title, $su, $district, $grade_info, $grade_level, $act73]) {
  $existing = $node_storage->loadByProperties([
    'type'  => 'place',
    'title' => $title,
  ]);

  try {
    if ($existing) {
      /** @var \Drupal\node\NodeInterface $node */
      $node = reset($existing);
      $is_update = TRUE;
    }
    else {
      /** @var \Drupal\node\NodeInterface $node */
      $node = $node_storage->create([
        'type'   => 'place',
        'title'  => $title,
        'status' => 1,
      ]);
      $is_update = FALSE;
    }

    $node->set('body', [
      'value'  => "<p><strong>District:</strong> {$district} ({$su}). Grades: {$grade_info}.</p>",
      'format' => 'full_html',
    ]);

    if ($node->hasField('field_grade_levels')) {
      $node->set('field_grade_levels', $grade_level);
    }

    if ($node->hasField('field_act73_status')) {
      $node->set('field_act73_status', $act73);
    }

    if ($node->hasField('field_notes')) {
      $node->set('field_notes', "SU: {$su} | District: {$district} | Grades: {$grade_info}");
    }

    $node->save();
    if ($is_update) {
      echo "  ~ {$title} (nid {$node->id()}) — updated\n";
      $updated++;
    }
    else {
      echo "  ✓ {$title} — created\n";
      $created++;
    }
  }
  catch (\Exception $e) {
    echo "  ✗ {$title}: " . $e->getMessage() . "\n";
    $errors++;
  }
}

echo "\n=== Complete ===\n";
echo "Created:  {$created}\n";
echo "Updated:  {$updated}\n";
echo "Errors:   {$errors}\n\n";
echo "Next: run update-town-coordinates.php\n\n";
