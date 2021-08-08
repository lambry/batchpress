<?php

/**
 * The main jobs i.e. processes to run.
 */

namespace Lambry\BatchPress\Jobs;

if (!defined('ABSPATH')) exit;

class Import {
  public $batch = 10;
  public $upload = true;
  public $label = 'Import Items';

  /**
   * Format items.
   */
  public function items(array $data) : array {
    // Actually format data here
    return array_map(fn($item) => $item, $data);
  }

  /**
   * Process items.
   */
  public function process($item) {
    // Actually import items here and return any errors
    return rand(0, 10) >= 9 ? "Item {$item['id']} failed to import." : null;
  }
}
