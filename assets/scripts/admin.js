/* Run updater */
;(function($) {
  let job = null
  let ajax = null
  const bp = $('.batchpress')
  const message = $('.batchpress-message')
  const nonce = bp.find('.batchpress-form').data('nonce')

  // Events
  bp.on('submit', '.batchpress-form', start)
  bp.on('click', '.batchpress-stop', stop)
  bp.on('click', '.batchpress-back', back)

  // Make the ajax request
  function process(process) {
    ajax = $.post(ajaxurl, { action: 'batchpress', job, nonce, process })
      .done(done)
      .fail(failed)
  }

  // Start and setup queue
  function start(e) {
    e.preventDefault()

    job = bp.find('input[name=job]:checked').val()

    process('start')
    bp.addClass('batchpress-processing')
    message.html(`<small>${job}</small> ${message.data('message')}`)
  }

  // Stop and clear queue
  function stop() {
    try {
      ajax.abort()
    } catch (error) {
      console.error(`Abort: ${error}`);
    } finally {
      process('stop')
      bp.removeClass('batchpress-processing')
    }
  }

  // Handle ajax done event
  function done(data) {
    data = $.parseJSON(data)
    status(data)

    if (data.status === 'processing') {
      return process('run')
    }
  }

  // Handle ajax failed event
  function failed(data) {
    status($.parseJSON(data))
    bp.addClass('batchpress-error')
  }

  // Update the on page status
  function status(data) {
    message.html(`<small>${job}</small> ${data.message}`)
    bp.addClass('batchpress-' + data.status)
  }

  // Go back to start screen
  function back() {
    bp.removeClass('batchpress-done batchpress-error batchpress-processing')
  }
})(jQuery)
