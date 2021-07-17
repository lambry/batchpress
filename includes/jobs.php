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
  public $list = ['import' => 'Import Items', 'update' => 'Update Items'];

  /**
   * Get items to import.
   */
  public function getImport() : array {
    // Actually get data here
    return range(1, 100);
  }

  /**
   * Get items to import.
   */
  public function processImport($item) {
    // Actually import items here
    return "Item {$item} successfully imported";
  }

  /**
   * Get items to update.
   */
  public function getUpdate() : array {
    // Actually get data here
    return range(1, 100);
  }

  /**
   * Get items to update.
   */
  public function processUpdate($item) {
    // Actually import items here
    return "Item {$item} successfully updated";
  }
}
