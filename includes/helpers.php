<?php

/**
 * Helpers for generic tasks.
 */

namespace Lambry\BatchPress\Core;

if (!defined('ABSPATH')) exit;

trait Helpers
{
  /**
   * Helper to a parse a file.
   */
  private function parseFile($file)
  {
    $rows = [];
    $file = fopen($file['tmp_name'], 'r');

    while (($row = fgetcsv($file)) !== false) $rows[] = $row;

    $headers = array_map('sanitize_text_field', array_map('sanitize_title_with_dashes', array_shift($rows)));

    return array_map(fn ($row) => array_combine($headers, $row), $rows);
  }

  /**
   * Helper to prepare/check an upload.
   */
  private function prepareUpload($url, $title)
  {
    $this->includeMediaHandling();

    $name = $title ? $title : basename($url);

    $file = get_page_by_title($name, OBJECT, 'attachment');

    $upload = $file ? $file->ID : (bool) filter_var(esc_url($url), FILTER_VALIDATE_URL);

    return [$upload, $name];
  }

  /**
   * Helper to upload an image.
   */
  private function uploadImage($url, $id, $title = null)
  {
    [$upload, $name] = $this->prepareUpload($url, $title);

    if ($upload !== true) return $upload;

    $image_id = media_sideload_image($url, $id, $name, 'id');

    return !is_wp_error($image_id) ? $image_id : null;
  }

  /**
   * Helper to upload a file.
   */
  private function uploadFile($url, $id, $title = null)
  {
    [$upload, $name] = $this->prepareUpload($url, $title);

    if ($upload !== true) return $upload;

    $file = ['name' => $name, 'tmp_name' => download_url($url)];

    $file_id = media_handle_sideload($file, $id);

    return !is_wp_error($file_id) ? $file_id : null;
  }

  /**
   * Include needed files for upload helpers.
   */
  private function includeMediaHandling(): void
  {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
  }
}
