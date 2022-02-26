<?php

/**
 * Handle setting up the plugins assets, page etc.
 */

namespace Lambry\BatchPress\Core;

if (!defined('ABSPATH')) exit;

class Setup
{
  private $page;
  private $jobs;

  /**
   * Set vars and add actions.
   */
  public function __construct()
  {
    add_action('admin_menu', [$this, 'menu']);
    add_action('admin_enqueue_scripts', [$this, 'assets']);
    add_action('after_setup_theme', [$this, 'jobs']);
  }

  /**
   * Adds the appropriate menu type.
   */
  public function menu()
  {
    $this->page = add_management_page('BatchPress', 'BatchPress', 'manage_options', 'batchpress', [$this, 'page']);
  }

  /**
   * Register page and contents.
   */
  public function page()
  {
    require_once BATCHPRESS_INCLUDES . 'page.php';
  }


  /**
   * Include the needed assets.
   */
  public function assets($hook)
  {
    if ($this->page !== $hook) return;

    wp_enqueue_style('batchpress-styles', BATCHPRESS_ASSETS . 'styles/admin.css', [], BATCHPRESS_VERSION);
    wp_enqueue_script('batchpress-scripts', BATCHPRESS_ASSETS . 'scripts/admin.min.js', ['jquery'], BATCHPRESS_VERSION, true);
  }

  /**
   * Get all jobs.
   */
  public function jobs()
  {
    $jobs = [];
    $registered = apply_filters('batchpress/jobs', []);

    foreach ($registered as $job) {
      $jobs[str_replace('\\', '-', $job)] = new $job();
    }

    $this->jobs = $jobs;
    $this->updater = new Updater($this->jobs);

    return $jobs;
  }
}
