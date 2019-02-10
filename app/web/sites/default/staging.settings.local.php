<?php
$hostname = str_replace(".","\.", getenv('DRUPAL_WEBSITE_HOSTNAME'));
$dbpassword = getenv('DRUPAL_DATABASE_PASSWORD');
$dbusername = getenv('DRUPAL_DATABASE_USER');
$dbname = getenv('DRUPAL_DATABASE_NAME');
$dbhost = getenv('DRUPAL_DATABASE_HOSTNAME');

$databases['default']['default'] = array (
  'database' => $dbname,
  'username' => $dbusername,
  'password' => $dbpassword,
  'prefix' => '',
  'host' => $dbhost,
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$settings['install_profile'] = 'standard';


$settings['trusted_host_patterns'] = array(
  '^xeer\.ch$',
  '^.+\.xeer\.ch$',
  '^xeer\.localhost$',
  '^' . $hostname . '$'
);
