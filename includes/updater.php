<?php

/**
 * The main updater page and functions.
 *
 * @package BatchPress
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

class Updater {
  private $data;

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
    $this->data = $_POST;

    if (!$this->valid()) {
      $this->response('error', 'This seems fishy');
    }

    if ($this->data['process'] ===  'start') {
      $this->start();
    }
    if ($this->data['process'] === 'stop') {
      $this->stop();
    }

    $this->run();
  }

  /**
   * Run any setup before starting the process for the first time.
   */
  public function start() {
    $items = get_option('batchpress');

    if (! $items) {
      // Actually get data here
      $items = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    }

    update_option('batchpress', $items);

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)));
  }

  /**
   * Run any teardown when stopping the process.
   */
  public function stop() {
    update_option('batchpress', []);

    $this->response('stop', __('Processing...', 'batchpress'));
  }

  /**
   * Run the actual batch operation.
   */
  public function run() : bool
  {
    $items = get_option('batchpress');

    // Actually process data here
    // Then remove processed $items

    array_shift($items);

    update_option('batchpress', $items);

    if (! $items) {
      $this->response('done', __('Finished processing!'));
    }

    $this->response('processing', sprintf(_n('%d item remaining', '%d items remaining', count($items), 'batchpress'), count($items)));
  }

  /**
   * Check for action validity.
   */
  public function valid() : bool {
    return $this->data['action'] === 'batchpress' && check_ajax_referer('batchpress', 'nonce', false);
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
