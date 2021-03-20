<?php
/**
 * Plugin Name: BatchPress
 * Plugin URI: https://github.com/lambry/batchpress
 * Description: A starter plugin to help process data in batches.
 * Version: 0.2.1
 * Author: Lambry
 * Author URI: https://lambry.com
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

define('BATCHPRESS_VERSION', '0.2.1');
define('BATCHPRESS_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
define('BATCHPRESS_INCLUDES', plugin_dir_path(__FILE__) . 'includes/');

require_once BATCHPRESS_INCLUDES . 'updater.php';
require_once BATCHPRESS_INCLUDES . 'jobs.php';
require_once BATCHPRESS_INCLUDES . 'plugin.php';

add_action('plugins_loaded', function() {
  if (is_admin()) new Plugin();
});
