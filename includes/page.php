<div class="wrap batchpress" data-nonce="<?= wp_create_nonce('batchpress'); ?>">
  <h1 class="batchpress-heading">
    <?php _e('BatchPress', 'batchpress'); ?>
  </h1>

  <form class="batchpress-form" enctype="multipart/form-data">
    <p><?php _e('Choose a job to run below', 'batchpress'); ?></p>

    <ul class="batchpress-jobs">
      <?php foreach($this->jobs as $name => $job) : ?>
        <li>
          <label class="batchpress-jobs-job">
            <input type="radio" name="job" value="<?= $name; ?>" class="batchpress-option" data-label="<?= $job->label ?? ''; ?>" data-upload="<?= $job->upload ?? 0; ?>">
            <h3 class="batchpress-jobs-label"><?= $job->label; ?></h3>
            <?php if (property_exists($job, 'description')) : ?>
              <p class="batchpress-jobs-description"><?= $job->description; ?></p>
            <?php endif; ?>
          </label>
        </li>
      <?php endforeach; ?>
    </ul>

    <label class="batchpress-upload">
      <?php _e('Select CSV file', 'batchpress'); ?>
      <input type="file" name="file" class="batchpress-file" accept=".csv">
    </label>

    <button type="submit" class="batchpress-button button button-primary button-large" disabled>
      <?php _e('Run', 'batchpress'); ?>
    </button>
  </form>

  <div class="batchpress-process">
    <div class="batchpress-message" data-message="<?php _e('Processing...', 'batchpress'); ?>">
      <small class="batchpress-message-job"></small>
      <h4 class="batchpress-message-status"></h4>
    </div>
    <div class="batchpress-log">
      <h4 class="batchpress-log-heading"><?php _e('Log', 'batchpress'); ?> <span class="batchpress-log-count">0</span></h4>
      <ul></ul>
    </div>

    <div class="batchpress-actions">
      <button type="button" class="batchpress-stop button button-secondary">
        <?php _e('Stop processing', 'batchpress'); ?>
      </button>

      <button type="button" class="batchpress-back button button-secondary">
        <?php _e('Go back', 'batchpress'); ?>
      </button>
    </div>
  </div>
</div>
