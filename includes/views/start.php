<h1 class="batchpress-heading">
  <?php _e('BatchPress', 'batchpress'); ?>
</h1>
<form class="batchpress-form" data-nonce="<?= wp_create_nonce('batchpress'); ?>">
  <p><?php _e('This is a starter plugin to help process data in batches.', 'batchpress'); ?></p>
  <p><strong><?php _e('BatchPress features:', 'batchpress'); ?></strong></p>
  <ul>
    <li><?php _e('A "Framework" for running batched processes.', 'batchpress'); ?></li>
    <li><?php _e('And UI for launching, monitoring and stoping batched processes.', 'batchpress'); ?></li>
  </ul>
  <p><strong>Choose process to run</strong></p>
  <p>
    <label><input type="radio" name="job" value="import" checked="checked"> Import items</label><br>
    <label><input type="radio" name="job" value="update"> Update items</label>
  </p>
  <button type="submit" class="batchpress-button button button-primary button-large">
    <?php _e('Start', 'batchpress'); ?>
  </button>
</form>
