/* Run updater */
;(function($) {
  let ajax = null
  const batchPress = $('.batchpress')
  const message = $('.batchpress-message')
  const nonce = batchPress.find('.batchpress-form').data('nonce')

  batchPress.on('submit', '.batchpress-form', start)
  batchPress.on('click', '.batchpress-stop', stop)

  // Make the ajax request
  function process(process = 'process') {
    ajax = $.post(ajaxurl, { action: 'batchpress', nonce, process })
      .done(done)
      .fail(failed)
  }

  // Start and setup queue
  function start(e) {
    e.preventDefault()

    process('start')
    batchPress.addClass('batchpress-processing')
  }

  // Stop and clear queue
  function stop() {
    try {
      ajax.abort()
    } catch (error) {
      console.error(`Abort: ${error}`);
    } finally {
      process('stop')
      batchPress.removeClass('batchpress-processing')
    }
  }

  // Handle ajax done event
  function done(data) {
    data = $.parseJSON(data)
    status(data)

    if (data.status === 'processing') {
      return process()
    }
  }

  // Handle ajax failed event
  function failed(data) {
    status($.parseJSON(data))
    batchPress.addClass('batchpress-error')
  }

  // Update the on page status
  function status(data) {
    message.html(data.message)
    batchPress.addClass('batchpress-' + data.status)
  }
})(jQuery)
