<?php
$databases['default']['default'] = [
  'database' => 'drupal_vt',
  'username' => 'postgres',
  'password' => '',
  'prefix' => '',
  'host' => 'database',
  'port' => '5432',
  'isolation_level' => 'READ COMMITTED',
  'driver' => 'pgsql',
  'namespace' => 'Drupal\\pgsql\\Driver\\Database\\pgsql',
  'autoload' => 'core/modules/pgsql/src/Driver/Database/pgsql/',
];

$settings['hash_salt'] = 'hgk88PAggtje3XVJApwV77p3C851__ZiH8zbN7bBAZNbqP9xkw1rLTwK3LAG8VQW3atXeLk-xA';

$options['uri'] = "https://vtstudentsfirst.lndo.site/";
$options['base_url'] = "https://vtstudentsfirst.lndo.site/";

$settings['trusted_host_patterns'] = [
  '^vtstudentsfirst\.lndo\.site$',
  '^localhost'
];

$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

$config['environment_indicator.indicator']['name'] = 'Local';
$config['environment_indicator.indicator']['bg_color'] = '#505050';
$config['environment_indicator.indicator']['fg_color'] = '#ffffff';
