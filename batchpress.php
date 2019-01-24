<?php

/**
 * Plugin Name: BatchPress
 * Plugin URI: https://github.com/lambry/batchpress
 * Description: A starter plugin to help process data in batches.
 * Version: 0.1.0
 * Author: Lambry
 * Author URI: https://lambry.com
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

define('BATCHPRESS_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
define('BATCHPRESS_INCLUDES', plugin_dir_path(__FILE__) . 'includes/');

require_once BATCHPRESS_INCLUDES . 'updater.php';
require_once BATCHPRESS_INCLUDES . 'setup.php';

add_action('plugins_loaded', function() {
  if (is_admin()) {
    new Setup();
  }
});
