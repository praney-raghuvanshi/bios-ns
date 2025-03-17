/**
 * Add contingency Flight for Schedule
 */

'use strict';

$(document).ready(function () {
  // Validate AWB existence via AJAX
  $('.check-flight').on('click', function () {
    if ($('#addcontingencyFlightForScheduleForm').valid()) {
      let flightNumber = $('#flight').val();

      if (!flightNumber) {
        $('#flightMessage').html('<div class="text-danger">Please enter Flight number.</div>');
        return;
      }

      $('#flightMessage').html('');

      $.ajax({
        url: '/checkFlight',
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { flightNumber },
        success: function (response) {
          console.log(response);
          if (response.success) {
            if (response.fill) {
              let data = response.data;
              if (data.flight_type === 'inbound') {
                $('#inbound').prop('checked', true);
              } else if (data.flight_type === 'outbound') {
                $('#outbound').prop('checked', true);
              }
              $('#inbound').prop('disabled', false);
              $('#outbound').prop('disabled', false);
              $('#location').val(data.location_id);
              $('#location').prop('disabled', false);
              $('#arrivalDay').val(data.arrival_day);
              $('#arrivalDay').prop('disabled', false);
              $('#from').val(data.from);
              $('#from').prop('disabled', false);
              $('#to').val(data.to);
              $('#to').prop('disabled', false);
              $('#std').val(data.departure_time);
              $('#std').prop('disabled', false);
              $('#sta').val(data.arrival_time);
              $('#sta').prop('disabled', false);
              $('#aircraft').val(data.aircraft_id);
              $('#aircraft').prop('disabled', false);
              $('#effectiveDate').prop('disabled', false);
            } else {
              $('#inbound').prop('disabled', false);
              $('#outbound').prop('disabled', false);
              $('#location').prop('disabled', false);
              $('#arrivalDay').prop('disabled', false);
              $('#from').prop('disabled', false);
              $('#to').prop('disabled', false);
              $('#std').prop('disabled', false);
              $('#sta').prop('disabled', false);
              $('#aircraft').prop('disabled', false);
              $('#effectiveDate').prop('disabled', false);
            }
          } else {
            $('#flightMessage').html('<div class="text-danger">Error checking Flight.</div>');
          }
        },
        error: function () {
          $('#flightMessage').html('<div class="text-danger">Error checking Flight.</div>');
        }
      });
    }
  });

  // Initialize jQuery Validation
  $('#addcontingencyFlightForScheduleForm').validate({
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent().append('<div class="form-control-feedback"></div>'));
    },
    rules: {
      flight: {
        required: true
      },
      direction: {
        required: true
      },
      from: {
        required: true,
        notEqualTo: '#to'
      },
      to: {
        required: true,
        notEqualTo: '#from'
      },
      departure_time: {
        required: true
      },
      arrival_time: {
        required: true
      },
      arrival_day: {
        required: true
      },
      aircraft: {
        required: true
      }
    },
    messages: {
      from: {
        notEqualTo: 'From and To cannot be the same.'
      },
      to: {
        notEqualTo: 'From and To cannot be the same.'
      }
    }
  });
});
