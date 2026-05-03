<?php

/**
 * @file import-edu-organizations.php
 *
 * Imports the 18 Vermont independent schools eligible to receive public tuition
 * under Act 73 into the 'edu_organization' taxonomy vocabulary.
 *
 * Creates new terms or fully updates existing ones with all available data.
 *
 * Source: Vermont Agency of Education
 * https://education.vermont.gov/content/independent-schools
 *
 * Usage:
 *   lando drush php-script import-edu-organizations.php
 */

$vocab = 'edu_organization';

$schools = [
  [
    'name'        => 'St. Johnsbury Academy',
    'description' => 'Historic independent academy founded in 1842. One of Vermont\'s four historic town academies. Admits every Vermont student without academic requirements. 75% of students attend on public tuition. 20% receive special education services.',
    'website'     => 'https://stjacademy.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'St. Johnsbury',
      'postal_code'         => '05819',
      'address_line1'       => '1000 Main Street',
    ],
  ],
  [
    'name'        => 'Lyndon Institute',
    'description' => 'Historic independent academy founded in 1867. One of Vermont\'s four historic town academies, serving the Northeast Kingdom.',
    'website'     => 'https://www.lyndoninstitute.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Lyndon Center',
      'postal_code'         => '05850',
      'address_line1'       => '168 Institute Circle',
    ],
  ],
  [
    'name'        => 'Thetford Academy',
    'description' => 'Vermont\'s oldest independent school, founded in 1819. One of Vermont\'s four historic town academies, serving tuitioning students in Orange County.',
    'website'     => 'https://thetfordacademy.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Thetford Center',
      'postal_code'         => '05075',
      'address_line1'       => '304 Academy Road',
    ],
  ],
  [
    'name'        => 'Burr and Burton Academy',
    'description' => 'Historic independent academy founded in 1829 in Manchester, Vermont. One of Vermont\'s four historic town academies, serving tuitioning students in Bennington County.',
    'website'     => 'https://www.burrburton.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Manchester',
      'postal_code'         => '05254',
      'address_line1'       => '57 Seminary Avenue',
    ],
  ],
  [
    'name'        => 'Riverside School',
    'description' => 'Independent PreK-8 school in Lyndonville, Vermont. 71 percent of students attend on public tuition, serving some of the youngest tuitioning students in the NEK.',
    'website'     => 'https://www.theriversideschool.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Lyndonville',
      'postal_code'         => '05851',
      'address_line1'       => '30 Lily Pond Road',
    ],
  ],
  [
    'name'        => 'East Burke School',
    'description' => 'Independent alternative high school in East Burke, Vermont. One of the 18 schools eligible to receive public tuition under Act 73.',
    'website'     => 'https://www.eastburkeschool.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'East Burke',
      'postal_code'         => '05832',
      'address_line1'       => '490 Burke Hollow Road',
    ],
  ],
  [
    'name'        => 'Long Trail School',
    'description' => 'Independent school in Dorset, Vermont serving grades 6-12. 73% of students were publicly funded in 2023-24.',
    'website'     => 'https://longtrailschool.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Dorset',
      'postal_code'         => '05251',
      'address_line1'       => '1045 Kirby Hollow Road',
    ],
  ],
  [
    'name'        => 'Sharon Academy',
    'description' => 'Independent school serving grades 7-12 in Sharon, Vermont. Serves tuitioning students in the Upper Valley region.',
    'website'     => 'https://sharonacademy.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Sharon',
      'postal_code'         => '05065',
      'address_line1'       => '205 Academy Drive',
    ],
  ],
  [
    'name'        => 'Burke Mountain Academy',
    'description' => 'Independent ski academy in East Burke, Vermont. Founded 1970, the first ski academy in North America.',
    'website'     => 'https://www.burkemtnacademy.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'East Burke',
      'postal_code'         => '05832',
      'address_line1'       => '60 Alpine Lane',
    ],
  ],
  [
    'name'        => 'Killington Mountain School',
    'description' => 'Independent ski academy in Killington, Vermont. Longest-running winter-term ski academy in the U.S.',
    'website'     => 'https://www.killingtonmountainschool.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Killington',
      'postal_code'         => '05751',
      'address_line1'       => '2708 Killington Road',
    ],
  ],
  [
    'name'        => 'Stratton Mountain School',
    'description' => 'Independent ski academy at Stratton Mountain, Vermont. Founded 1972. First ski academy accredited by NEASC.',
    'website'     => 'https://www.gosms.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Stratton Mountain',
      'postal_code'         => '05155',
      'address_line1'       => '7 World Cup Circle',
    ],
  ],
  [
    'name'        => 'Okemo Mountain School',
    'description' => 'Independent ski and snowboard academy in Ludlow, Vermont. Founded 1991.',
    'website'     => 'https://www.okemomountainschool.org',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Ludlow',
      'postal_code'         => '05149',
      'address_line1'       => '53 Main Street',
    ],
  ],
  [
    'name'        => 'Maple Street School',
    'description' => 'Independent PreK-8 school in Manchester, Vermont.',
    'website'     => 'https://maplestreetschool.com',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Manchester',
      'postal_code'         => '05254',
      'address_line1'       => '322 Maple St',
    ],
  ],
  [
    'name'        => 'Mountain School at Winhall',
    'description' => 'Independent school in Winhall, Vermont.',
    'website'     => '',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Winhall',
      'postal_code'         => '05340',
      'address_line1'       => '9 School St',
    ],
  ],
  [
    'name'        => 'Thaddeus Stevens School',
    'description' => 'Independent school in Craftsbury, Vermont.',
    'website'     => '',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'East Burke',
      'postal_code'         => '05832',
      'address_line1'       => '638 VT-114',
    ],
  ],
  [
    'name'        => 'Expeditionary School of Black River',
    'description' => 'Independent school in Ludlow, Vermont.',
    'website'     => '',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'Ludlow',
      'postal_code'         => '05149',
      'address_line1'       => '43 Main St',
    ],
  ],
  [
    'name'        => 'Southshire Community School',
    'description' => 'Independent school in North Bennington, Vermont.',
    'website'     => '',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'North Bennington',
      'postal_code'         => '05257',
      'address_line1'       => '24 Bank St',
    ],
  ],
  [
    'name'        => 'Village School of North Bennington',
    'description' => 'Independent school in North Bennington, Vermont.',
    'website'     => '',
    'address'     => [
      'country_code'        => 'US',
      'administrative_area' => 'VT',
      'locality'            => 'North Bennington',
      'postal_code'         => '05257',
      'address_line1'       => '9 School St',
    ],
  ],
];

$term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
$created = 0;
$updated = 0;

echo "\n=== Vermont Eligible Schools Import ===\n";
echo "Vocabulary: {$vocab}\n";
echo "Total schools: " . count($schools) . "\n\n";

foreach ($schools as $school) {
  $existing = $term_storage->loadByProperties([
    'name' => $school['name'],
    'vid'  => $vocab,
  ]);

  if ($existing) {
    $term = reset($existing);

    // Always overwrite all fields with latest data.
    $term->set('description', [
      'value'  => $school['description'],
      'format' => 'plain_text',
    ]);

    if ($term->hasField('schema_address')) {
      $term->set('schema_address', $school['address']);
    }

    if ($term->hasField('field_website')) {
      if (!empty($school['website'])) {
        $term->set('field_website', ['uri' => $school['website'], 'title' => $school['name']]);
      }
      else {
        $term->set('field_website', []);
      }
    }

    $term->save();
    echo "  ~ Updated: {$school['name']}\n";
    $updated++;
    continue;
  }

  // Create new term.
  $term = $term_storage->create([
    'name' => $school['name'],
    'vid'  => $vocab,
    'description' => [
      'value'  => $school['description'],
      'format' => 'plain_text',
    ],
  ]);

  if (!empty($school['address']['locality'])) {
    $term->set('schema_address', $school['address']);
  }

  if (!empty($school['website'])) {
    $term->set('field_website', ['uri' => $school['website'], 'title' => $school['name']]);
  }

  $term->save();
  echo "  + Created: {$school['name']} ({$school['address']['locality']}, VT)\n";
  $created++;
}

echo "\n=== Complete ===\n";
echo "Created: {$created}\n";
echo "Updated: {$updated}\n\n";
echo "Street addresses still needed (verify manually):\n";
echo "  - Maple Street School (Manchester)\n";
echo "  - Mountain School at Winhall\n";
echo "  - Thaddeus Stevens School (Craftsbury)\n";
echo "  - Expeditionary School of Black River (Ludlow)\n";
echo "  - Southshire Community School (North Bennington)\n";
echo "  - Village School of North Bennington\n\n";
