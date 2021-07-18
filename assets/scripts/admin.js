/* Run updater */
(function($) {
  let job = null
  let ajax = null
  const bp = $('.batchpress')
  const message = $('.batchpress-message')
  const errors = $('.batchpress-errors')
  const nonce = bp.find('.batchpress-form').data('nonce')

  // Events
  bp.on('click', '.batchpress-option', ready)
  bp.on('submit', '.batchpress-form', start)
  bp.on('click', '.batchpress-stop', stop)
  bp.on('click', '.batchpress-back', back)

  // Start and setup queue
  function ready() {
    bp.find('.batchpress-button').attr('disabled', false)
  }

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
      console.error(`Abort: ${error}`)
    } finally {
      clear()
      process('stop')
      bp.removeClass('batchpress-processing batchpress-error')
    }
  }

  // Handle ajax done event
  function done(data) {
    data = parse(data)

    status(data)

    if (data.status === 'processing') {
      return process('run')
    }
  }

  // Handle ajax failed event
  function failed(data) {
    status(parse(data))
    bp.addClass('batchpress-error')
  }

  // Update the on page status
  function status(data) {
    message.html(`<small>${job}</small> ${data.message}`)
    bp.addClass('batchpress-' + data.status)
    log(data.errors)
  }

  // Update the error log
  function log(log) {
    const count = Number(errors.find('.batchpress-errors-count').html());
    errors.find('ul').append(log.map(entry => `<li>${entry}</li>`))
    errors.find('.batchpress-errors-count').html(count + log.length)
  }

  // Clear logs
  function clear() {
    errors.find('ul').empty()
    errors.find('.batchpress-errors-count').html(0)
  }

  // Go back to start screen
  function back() {
    clear()
    bp.removeClass('batchpress-done batchpress-error batchpress-processing')
  }

  // Parse JSON response
  function parse(data) {
    try {
      return $.parseJSON(data)
    } catch (error) {
      return { status: 'error', message: error }
    }
  }
})(jQuery)
