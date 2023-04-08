/* Run updater */
(function($) {
  let ajax = null
  let form = null
  const bp = $('.batchpress')
  const upload = $('.batchpress-upload')
  const message = $('.batchpress-message')
  const log = $('.batchpress-log')
  const nonce = bp.data('nonce')

  // Events
  bp.on('click', '.batchpress-option', option)
  bp.on('submit', '.batchpress-form', submit)
  bp.on('click', '.batchpress-stop', stop)
  bp.on('click', '.batchpress-back', back)

  // Show upload or ready form
  function option() {
    upload.toggleClass('batchpress-show', $(this).data('upload') === 1)

    bp.find('.batchpress-button').attr('disabled', false)
  }

  // Make the ajax request
  function process(process) {
    form.append('process', process)

    ajax = $.ajax({
      method: 'POST',
      url: ajaxurl,
      processData: false,
      contentType: false,
      data: form
    }).done(done).fail(failed)
  }

  // Handle form submission
  function submit(e) {
    e.preventDefault()

    const option = bp.find('input[name=job]:checked')

    form = new FormData()
    form.append('job', option.val())
    form.append('nonce', nonce)
    form.append('action', 'batchpress')
    form.append('file', null)

    if (option.data('upload') === 1) {
      const files = upload.find('input')[0].files
      form.append('file', files.length ? files[0] : null)

      process('upload')
    } else {
      process('start')
    }

    bp.addClass('batchpress-processing')
    message.find('.batchpress-message-job').html(option.data('title') || form.get('job').replaceAll('-', ' '))
    message.find('.batchpress-message-status').html(message.data('message'))
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

  // Update the on page status and log
  function status(data) {
    message.find('.batchpress-message-status').html(data.message)
    bp.addClass('batchpress-' + data.status)

    if (data.errors) {
      const count = Number(log.find('.batchpress-log-count').html())

      log.find('ul').append(data.errors.map(error => `<li>${error}</li>`))
      log.find('.batchpress-log-count').html(count + data.errors.length)
    }
  }

  // Clear logs
  function clear() {
    log.find('ul').empty()
    log.find('.batchpress-log-count').html(0)
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
