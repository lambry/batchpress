<?php

/**
 * The main jobs i.e. processes to run.
 *
 * @package BatchPress
 */

namespace Lambry\BatchPress;

if (!defined('ABSPATH')) exit;

class Jobs {
  use Helpers;

  public $batch = 10;
  public $list = ['import' => 'Import Items'];

  /**
   * Get items to import.
   */
  public function getImport() : array {
    // Actually get data here
    return array_fill(0, 100, true);
  }

  /**
   * Get items to import.
   */
  public function processImport($item) : void {
    // Actually import items here
  }
}
