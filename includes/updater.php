<?php

/**
 * The main updater page and functions.
 *
 * @package BatchPress
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

class Updater {
  private $job;
  private $method;
  private $option;
  private $batch = 10;

  /**
   * Add actions.
   */
  function __construct() {
    add_action('wp_ajax_batchpress', [$this, 'process']);
  }

  /**
   * Run the main updater process.
   */
  public function process() {
    if ($this->isInvalid()) {
      $this->response('error', 'This seems fishy');
    }

    $this->job = sanitize_text_field($_POST['job']);
    $this->method = implode(array_map(fn($s) => ucfirst($s), explode('-', $this->job)));
    $this->option = 'batchpress-' . $this->job;

    $process = sanitize_text_field($_POST['process']);

    $this->$process();
  }

  /**
   * Run any setup before starting the process for the first time.
   */
  public function start() {
    $items = get_option($this->option);

    if (! $items) {
      $items = $this->{"get{$this->method}"}();
    }

    update_option($this->option, $items);

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)));
  }

  /**
   * Run any teardown when stopping the process.
   */
  public function stop() {
    update_option($this->option, []);

    $this->response('stop', __('Processing...', 'batchpress'));
  }

  /**
   * Run the actual batch operation.
   */
  public function run() : bool {
    $items = get_option($this->option);

    $batch = array_splice($items, 0, $this->batch);

    $this->{"process{$this->method}"}($batch);

    update_option($this->option, $items);

    if (! $items) {
      $this->response('done', __('Finished processing!'));
    }

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)));
  }

  /**
   * Get items to import.
   */
  public function getImport() : array {
    // Actually get data here
    return array_fill(0, 50, true);
  }

  /**
   * Get items to import.
   */
  public function processImport() : void {
    // Actually import items here
  }

  /**
   * Get items to import.
   */
  public function getUpdate() : array {
    // Actually get data here
    return array_fill(0, 100, true);
  }

  /**
   * Get items to import.
   */
  public function processUpdate() : void {
    // Actually update items here
  }

  /**
   * Check action validity.
   */
  public function isInvalid() : bool {
    return $_POST['action'] !== 'batchpress' || ! check_ajax_referer('batchpress', 'nonce', false);
  }

  /**
   * Return response data.
   */
  public function response(string $status, string $message) {
    echo json_encode([
      'status' => $status,
      'message' => $message
    ]);

    die();
  }
}
