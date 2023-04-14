<?php

/**
 * Plugin Name: BatchPress
 * Plugin URI: https://github.com/lambry/batchpress
 * Description: A little plugin to help process data in batches.
 * Version: 0.4.2
 * Author: Lambry
 * Author URI: https://lambry.com/
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

define('BATCHPRESS_VERSION', '0.4.2');
define('BATCHPRESS_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
define('BATCHPRESS_INCLUDES', plugin_dir_path(__FILE__) . 'includes/');

class Init
{
  /**
   * Add actions.
   */
  public function __construct()
  {
    if (is_admin()) {
      $this->includes();

      add_action('plugins_loaded', fn () => new Setup());
      add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'links']);
    }
  }

  /**
   * Required files.
   */
  public function includes() : void
  {
    require_once BATCHPRESS_INCLUDES . 'helpers.php';
    require_once BATCHPRESS_INCLUDES . 'updater.php';
    require_once BATCHPRESS_INCLUDES . 'setup.php';
  }

  /**
   * Add action links to plugins page
   */
  public function links(array $links): array
  {
    return array_merge([
      '<a href="' . admin_url('tools.php?page=batchpress') . '">' . __('Dashboard', 'batchpress') . '</a>',
    ], $links);
  }
}

new Init();
