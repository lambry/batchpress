<?php

/**
 * The main updater page and functions.
 *
 * @package BatchPress
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

class Updater {
  private $jobs;
  private $method;
  private $option;
  private $batch = 10;

  /**
   * Add actions.
   */
  function __construct() {
    $this->jobs = new Jobs();

    add_action('wp_ajax_batchpress', [$this, 'process']);
  }

  /**
   * Run the main updater process.
   */
  public function process() {
    if ($this->isInvalid()) {
      $this->response('error', 'This seems fishy');
    }

    $job = sanitize_text_field($_POST['job']);
    $process = sanitize_text_field($_POST['process']);

    $this->option = "batchpress-{$job}";
    $this->method = implode(array_map(fn($s) => ucfirst($s), explode('-', $job)));

    $this->$process();
  }

  /**
   * Run any setup before starting the process for the first time.
   */
  private function start() {
    $items = get_option($this->option);

    if (! $items) {
      $items = $this->jobs->{"get{$this->method}"}();
    }

    update_option($this->option, $items);

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)));
  }

  /**
   * Run any teardown when stopping the process.
   */
  private function stop() {
    update_option($this->option, []);

    $this->response('stop', __('Processing...', 'batchpress'));
  }

  /**
   * Run the actual batch operation.
   */
  private function run() : bool {
    $items = get_option($this->option);
    $batch = array_splice($items, 0, $this->batch);

    foreach($batch as $item) {
      $this->jobs->{"process{$this->method}"}($item);
    }

    update_option($this->option, $items);

    if (! $items) {
      $this->response('done', __('Finished processing!'));
    }

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)));
  }

  /**
   * Check action validity.
   */
  private function isInvalid() : bool {
    return $_POST['action'] !== 'batchpress' || ! check_ajax_referer('batchpress', 'nonce', false);
  }

  /**
   * Return response data.
   */
  private function response(string $status, string $message) {
    echo json_encode([
      'status' => $status,
      'message' => $message
    ]);

    die();
  }
}
