<?php

/**
 * Handle setting up the plugins views, assets etc.
 *
 * @package BatchPress
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

class Setup {
  private $page;
  private $updater;

  /**
   * Set vars, and add actions.
   */
  public function __construct() {
    add_action('admin_menu', [$this, 'menu']);
    add_action('admin_enqueue_scripts', [$this, 'assets']);

    $this->updater = new Updater();
  }

  /**
   * Adds the appropriate menu type.
   */
  public function menu() {
    $this->page = add_management_page('BatchPress', 'BatchPress', 'manage_options', 'batchpress', [$this, 'page']);
  }

  /**
   * Include the needed assets.
   */
  public function assets($hook) {
    if ($this->page !== $hook) return;

    wp_enqueue_style('batchpress-styles', BATCHPRESS_ASSETS . 'styles/admin.css', [], BATCHPRESS_VERSION);
    wp_enqueue_script('batchpress-scripts', BATCHPRESS_ASSETS . 'scripts/admin.js', ['jquery'], BATCHPRESS_VERSION, true);
  }

  /**
   * Register page and contents.
   */
  public function page() { ?>
    <div class="wrap batchpress">
      <?php
        require_once BATCHPRESS_INCLUDES . 'views/start.php';
        require_once BATCHPRESS_INCLUDES . 'views/process.php';
      ?>
    </div>
  <?php }
}
