<?php

$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/default/development.services.yml';

$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';

$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$config['system.performance']['minifyhtml']['minify_html'] = FALSE;


/***
 * Set development vagrant local to use /var/www/uwmed/private_files
 */
$settings['file_private_path'] = dirname($app_root) . '/private_files';

/***
 * Include any special settings for testing on http://stevie.uwmed.local/
 */
$file = $settings['file_private_path'] . '/stevie.uwmed.local.settings.php';
if (file_exists($file)) {
  include $file;
}

