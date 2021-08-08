<?php

/**
 * The main jobs i.e. processes to run.
 */

namespace Lambry\BatchPress\Jobs;

if (!defined('ABSPATH')) exit;

class Update {
  public $batch = 20;
  public $label = 'Update Items';

  /**
   * Get items.
   */
  public function items() : array {
    // Actually get data here
    return range(1, 100);
  }

  /**
   * Process items.
   */
  public function process($item) {
    // Actually update items here and return any errors
    return rand(0, 10) >= 9 ? "Item {$item} failed to update." : null;
  }
}
