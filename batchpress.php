<?php
/**
 * Plugin Name: BatchPress
 * Plugin URI: https://github.com/lambry/batchpress
 * Description: A starter plugin to help process data in batches.
 * Version: 0.3.0
 * Author: Lambry
 * Author URI: https://lambry.com/
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

define('BATCHPRESS_VERSION', '0.3.0');
define('BATCHPRESS_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
define('BATCHPRESS_CORE', plugin_dir_path(__FILE__) . 'core/');
define('BATCHPRESS_JOBS', plugin_dir_path(__FILE__) . 'jobs/');

require_once BATCHPRESS_CORE . 'helpers.php';
require_once BATCHPRESS_CORE . 'updater.php';
require_once BATCHPRESS_CORE . 'setup.php';

add_action('plugins_loaded', function() {
  if (is_admin()) new Core\Setup();
});
