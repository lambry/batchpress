<?php

/**
 * The main class for processing jobs.
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

class Updater
{
  use Helpers;

  private $job;
  private array $jobs;
  private string $option;
  private string $log;

  /**
   * Add actions.
   */
  function __construct(array $jobs)
  {
    $this->jobs = $jobs;

    add_action('wp_ajax_batchpress', [$this, 'process']);
  }

  /**
   * Run the main updater process.
   */
  public function process() : void
  {
    if ($this->isInvalid()) {
      $this->response('error', 'This seems fishy');
    }

    $job = sanitize_text_field($_POST['job']);
    $process = sanitize_text_field($_POST['process']);

    $this->job = $this->jobs[$job];
    $this->option = "batchpress-{$job}";
    $this->log = "batchpress-{$job}-log";

    $this->$process();
  }

  /**
   * Get file ready before starting, or continue previous import.
   */
  private function upload() : void
  {
    $file = $_FILES['file'] ?? null;
    $items = get_option($this->option, []);
    $log = get_option($this->log, []);

    if ($items) {
      $this->response('processing', sprintf(_n('%d item processing', '%d items processing', count($items),  'batchpress'), count($items)), $log);
    }

    if (!$file) {
      $this->response('error', 'Please select a file');
    }

    $items = $this->parseFile($file);

    if (method_exists($this->job, 'items')) {
      $items = $this->job->items($items);
    }

    update_option($this->log, []);
    update_option($this->option, $items);

    $this->response('processing', sprintf(_n('%d item pending', '%d items pending', count($items), 'batchpress'), count($items)));
  }

  /**
   * Run any setup before starting the process for the first time.
   */
  private function start() : void
  {
    $items = get_option($this->option, []);
    $log = get_option($this->log, []);

    if (!$items) {
      $log = [];
      $items = $this->job->items();
    }

    update_option($this->option, $items);
    update_option($this->log, $log);

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)), $log);
  }

  /**
   * Run any teardown when stopping the process.
   */
  private function stop() : void
  {
    update_option($this->option, []);
    update_option($this->log, []);

    $this->response('stop', __('Processing...', 'batchpress'));
  }

  /**
   * Run the actual batch operation.
   */
  private function run(): void
  {
    $items = get_option($this->option, []);
    $batch = array_splice($items, 0, $this->job->batch ?? 10);

    // Process each item in the batch and return any log messages
    $log = array_values(array_filter(array_map(fn ($item) => $this->job->process($item) ?: null, $batch)));

    update_option($this->option, $items);

    if ($log) {
      update_option($this->log, array_merge(get_option($this->log, []), $log));
    }

    if (!$items) {
      $this->response('done', __('Finished processing!'), $log);
    }

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)), $log);
  }

  /**
   * Check action validity.
   */
  private function isInvalid(): bool
  {
    return $_POST['action'] !== 'batchpress' || !check_ajax_referer('batchpress', 'nonce', false);
  }

  /**
   * Return response data.
   */
  private function response(string $status, string $message, array $log = [])
  {
    echo json_encode([
      'status' => $status,
      'message' => $message,
      'log' => $log
    ]);

    die();
  }
}
