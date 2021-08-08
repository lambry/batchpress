<?php

/**
 * Helpers for generic tasks.
 */

namespace Lambry\BatchPress\Core;

if (!defined('ABSPATH')) exit;

trait Helpers {
  /**
   * Helper to a parse a file.
   */
  private function parseFile($file) {
    $data = file_get_contents($file['tmp_name']);
    $rows = explode(PHP_EOL, $data);
    $headers = str_getcsv(array_shift($rows));

    return array_map(function($row) use($headers) {
      return array_combine($headers, str_getcsv($row));
    }, $rows);
  }

  /**
   * Helper to upload an image.
   */
  private function uploadImage($url, $id) {
    if (! filter_var(esc_url($url), FILTER_VALIDATE_URL)) return null;

    $this->includeMediaHandling();

    $image_id = media_sideload_image($url, $id, basename($url), 'id');

    return !is_wp_error($image_id) ? $image_id : null;
  }

  /**
   * Helper to upload a file.
   */
  private function uploadFile($url, $id) {
    if (! filter_var($url, FILTER_VALIDATE_URL)) return null;

    $this->includeMediaHandling();

    $file = [
      'name' => basename($url),
      'tmp_name' => download_url($url)
    ];

    $file_id = media_handle_sideload($file, $id);

    return !is_wp_error($file_id) ? $file_id : null;
  }

  /**
   * Include needed files for upload helpers.
   */
  private function includeMediaHandling() : void {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
  }
}
