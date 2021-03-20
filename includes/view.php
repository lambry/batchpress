<div class="wrap batchpress">
  <h1 class="batchpress-heading">
    <?php _e('BatchPress', 'batchpress'); ?>
  </h1>

  <form class="batchpress-form" data-nonce="<?= wp_create_nonce('batchpress'); ?>">
    <h4><?php _e('Choose process to run', 'batchpress'); ?></h4>

    <ul>
      <?php foreach($jobs as $name => $description) : ?>
        <li>
          <label><input type="radio" name="job" value="<?= $name; ?>" checked="checked"> <?= $description; ?></label>
        </li>
      <?php endforeach; ?>
    </ul>

    <button type="submit" class="batchpress-button button button-primary button-large">
      <?php _e('Start', 'batchpress'); ?>
    </button>
  </form>

  <div class="batchpress-process">
    <div class="batchpress-message" data-message="<?php _e('Processing...', 'batchpress'); ?>"></div>

    <button type="button" class="batchpress-stop button button-secondary">
      <?php _e('Stop processing', 'batchpress'); ?>
    </button>

    <button type="button" class="batchpress-back button button-secondary">
      <?php _e('Go back', 'batchpress'); ?>
    </button>
  </div>
</div>
