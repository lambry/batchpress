<?php

/**
 * Handle setting up the plugins assets, page etc.
 */

namespace Lambry\BatchPress\Core;

if (!defined('ABSPATH')) exit;

class Setup {
  private $page;
  private $jobs;
  private $updater;

  /**
   * Set vars and add actions.
   */
  public function __construct() {
    $this->jobs = $this->jobs();
    $this->updater = new Updater($this->jobs);

    add_action('admin_menu', [$this, 'menu']);
    add_action('admin_enqueue_scripts', [$this, 'assets']);
  }

  /**
   * Get all jobs.
   */
  public function jobs() {
    $jobs = [];

    foreach (glob(BATCHPRESS_JOBS . '*.php') as $file) {
      require_once $file;

      $name = basename($file, '.php');
      $class = str_replace('-', '', ucwords($name, '-'));
      $instance = '\Lambry\BatchPress\Jobs\\' . $class;

      $jobs[$name] = new $instance();
    }

    return $jobs;
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
    wp_enqueue_script('batchpress-scripts', BATCHPRESS_ASSETS . 'scripts/admin.min.js', ['jquery'], BATCHPRESS_VERSION, true);
  }

  /**
   * Register page and contents.
   */
  public function page() {
    require_once BATCHPRESS_CORE . 'page.php';
  }
}
