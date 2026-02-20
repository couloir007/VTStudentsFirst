<?php

// phpcs:ignoreFile
$settings['file_private_path'] = dirname(DRUPAL_ROOT) . '/private';

$settings['config_sync_directory'] = dirname(DRUPAL_ROOT) . '/config/sync';
$databases['default']['default'] = [
  'database' => 'roundybr_drupal',
  'username' => 'roundybr',
  'password' => 'Fi8w0t8I4x',
  'prefix' => '',
  'host' => 'us17.acugis-dns.com',
  'port' => '5432',
  'isolation_level' => 'READ COMMITTED',
  'driver' => 'pgsql',
  'namespace' => 'Drupal\\pgsql\\Driver\\Database\\pgsql',
  'autoload' => 'core/modules/pgsql/src/Driver/Database/pgsql/',
];

$settings['hash_salt'] = 'hgk88PAggtje3XVJApwV77p3C851__ZiH8zbN7bBAZNbqP9xkw1rLTwK3LAG8VQW3atXeLk-xA';

$options['uri'] = "https://vtstudentsfirst.com/";
$options['base_url'] = "https://vtstudentsfirst.com/";

$settings['trusted_host_patterns'] = [
  '^vtstudentsfirst\.com$',
  '^localhost'
];


$config['environment_indicator.indicator']['name'] = 'AcuGIS';
$config['environment_indicator.indicator']['bg_color'] = '#e7131a';
$config['environment_indicator.indicator']['fg_color'] = '#ffffff';

$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * If there is a local settings file, then include it
 */
//$local_settings = __DIR__ . "/settings.local.php";
if (file_exists(__DIR__ . "/settings.local.php")) {
  $settings['container_yamls'][] = __DIR__ . '/local.services.yml';
  include __DIR__ . "/settings.local.php";
}
