/* Run updater */
(function($) {
  let ajax = null
  let form = null
  const bp = $('.batchpress')
  const upload = $('.batchpress-upload')
  const message = $('.batchpress-message')
  const errors = $('.batchpress-errors')
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
    message.find('.batchpress-message-job').html(form.get('job').replace('-', ' '))
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

  // Update the on page status
  function status(data) {
    message.find('.batchpress-message-status').html(data.message)
    bp.addClass('batchpress-' + data.status)
    log(data.errors)
  }

  // Update the error log
  function log(log) {
    const count = Number(errors.find('.batchpress-errors-count').html())

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
