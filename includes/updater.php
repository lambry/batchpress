<?php

/**
 * The main class for processing jobs.
 */

namespace Lambry\BatchPress\Core;

if (!defined('ABSPATH')) exit;

class Updater
{
  use Helpers;

  private $job;
  private $jobs;
  private $option;
  private $errors;

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
  public function process()
  {
    if ($this->isInvalid()) {
      $this->response('error', 'This seems fishy');
    }

    $job = sanitize_text_field($_POST['job']);
    $process = sanitize_text_field($_POST['process']);

    $this->job = $this->jobs[$job];
    $this->option = "batchpress-{$job}";
    $this->errors = "batchpress-{$job}-errors";

    $this->$process();
  }

  /**
   * Get file ready before starting, or continue previous import.
   */
  private function upload()
  {
    $file = $_FILES['file'] ?? null;
    $items = get_option($this->option, []);
    $errors = get_option($this->errors, []);

    if ($items) {
      $this->response('processing', sprintf(_n('%d item processing', '%d items processing', count($items),  'batchpress'), count($items)), $errors);
    }

    if (!$file) {
      $this->response('error', 'Please select a file');
    }

    $items = $this->parseFile($file);

    if (method_exists($this->job, 'items')) {
      $items = $this->job->items($items);
    }

    update_option($this->errors, []);
    update_option($this->option, $items);

    $this->response('processing', sprintf(_n('%d item pending', '%d items pending', count($items), 'batchpress'), count($items)));
  }

  /**
   * Run any setup before starting the process for the first time.
   */
  private function start()
  {
    $items = get_option($this->option, []);
    $errors = get_option($this->errors, []);

    if (!$items) {
      $errors = [];
      $items = $this->job->items();
    }

    update_option($this->option, $items);
    update_option($this->errors, $errors);

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)), $errors);
  }

  /**
   * Run any teardown when stopping the process.
   */
  private function stop()
  {
    update_option($this->option, []);
    update_option($this->errors, []);

    $this->response('stop', __('Processing...', 'batchpress'));
  }

  /**
   * Run the actual batch operation.
   */
  private function run(): bool
  {
    $items = get_option($this->option, []);
    $batch = array_splice($items, 0, $this->job->batch ?? 10);

    $errors = array_values(array_filter(array_map(function ($item) {
      return $this->job->process($item) ?: null;
    }, $batch)));

    update_option($this->option, $items);

    if ($errors) {
      update_option($this->errors, array_merge(get_option($this->errors, []), $errors));
    }

    if (!$items) {
      $this->response('done', __('Finished processing!'), $errors);
    }

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)), $errors);
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
  private function response(string $status, string $message, array $errors = [])
  {
    echo json_encode([
      'status' => $status,
      'message' => $message,
      'errors' => $errors
    ]);

    die();
  }
}
