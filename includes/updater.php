<?php

/**
 * The main class for processing jobs.
 *
 * @package BatchPress
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

class Updater {
  private $jobs;
  private $option;
  private $errors;
  private $method;

  /**
   * Add actions.
   */
  function __construct(Jobs $jobs) {
    $this->jobs = $jobs;

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
    $this->errors = "batchpress-{$job}-errors";
    $this->method = implode(array_map(fn($s) => ucfirst($s), explode('-', $job)));

    $this->$process();
  }

  /**
   * Run any setup before starting the process for the first time.
   */
  private function start() {
    $items = get_option($this->option, []);
    $errors = get_option($this->errors, []);

    if (! $items) {
      $errors = [];
      $items = $this->jobs->{"get{$this->method}"}();
    }

    update_option($this->option, $items);
    update_option($this->errors, $errors);

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)), $errors);
  }

  /**
   * Run any teardown when stopping the process.
   */
  private function stop() {
    update_option($this->option, []);
    update_option($this->errors, []);

    $this->response('stop', __('Processing...', 'batchpress'));
  }

  /**
   * Run the actual batch operation.
   */
  private function run() : bool {
    $items = get_option($this->option, []);
    $batch = array_splice($items, 0, $this->jobs->batch);

    $errors = array_values(array_filter(array_map(function($item) {
      return $this->jobs->{"process{$this->method}"}($item) ?: null;
    }, $batch)));

    update_option($this->option, $items);

    if ($errors) {
      update_option($this->errors, array_merge(get_option($this->errors, []), $errors));
    }

    if (! $items) {
      $this->response('done', __('Finished processing!'), $errors);
    }

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)), $errors);
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
  private function response(string $status, string $message, array $errors = []) {
    echo json_encode([
      'status' => $status,
      'message' => $message,
      'errors' => $errors
    ]);

    die();
  }
}
