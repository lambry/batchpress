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
  private $job;
  private $log;
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

    $this->job = "batchpress-{$job}";
    $this->log = "batchpress-{$job}-log";
    $this->method = implode(array_map(fn($s) => ucfirst($s), explode('-', $job)));

    $this->$process();
  }

  /**
   * Run any setup before starting the process for the first time.
   */
  private function start() {
    $items = get_option($this->job, []);
    $log = get_option($this->log, []);

    if (! $items) {
      $log = [];
      $items = $this->jobs->{"get{$this->method}"}();
    }

    update_option($this->job, $items);
    update_option($this->log, $log);

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)), $log);
  }

  /**
   * Run any teardown when stopping the process.
   */
  private function stop() {
    update_option($this->job, []);
    update_option($this->log, []);

    $this->response('stop', __('Processing...', 'batchpress'));
  }

  /**
   * Run the actual batch operation.
   */
  private function run() : bool {
    $items = get_option($this->job, []);
    $batch = array_splice($items, 0, $this->jobs->batch);

    $processed = array_filter(array_map(function($item) {
      return $this->jobs->{"process{$this->method}"}($item) ?: null;
    }, $batch));

    update_option($this->job, $items);

    if ($processed) {
      $log = get_option($this->log, []);

      update_option($this->log, array_merge($log, $processed));
    }

    if (! $items) {
      $this->response('done', __('Finished processing!'), $processed);
    }

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)), $processed);
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
  private function response(string $status, string $message, array $log = []) {
    echo json_encode([
      'status' => $status,
      'message' => $message,
      'log' => $log
    ]);

    die();
  }
}
