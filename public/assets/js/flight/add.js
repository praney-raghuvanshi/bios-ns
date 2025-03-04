/**
 * Add Flight
 */

'use strict';

$(document).ready(function () {
  // Custom rule for "From" and "To" fields not being the same
  $.validator.addMethod(
    'notEqualTo',
    function (value, element, param) {
      return this.optional(element) || value !== $(param).val();
    },
    'From and To cannot be the same.'
  );

  // Initialize jQuery Validation
  $('#addFlightForm').validate({
    ignore: ':disabled', // Ignore disabled fields
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
      i_flight: {
        required: function () {
          return !$('#inboundForm input, #inboundForm select').prop('disabled');
        }
      },
      i_from: {
        required: true,
        notEqualTo: '#i_to'
      },
      i_to: {
        required: true,
        notEqualTo: '#i_from'
      },
      i_departure_time: {
        required: true
      },
      i_arrival_time: {
        required: true
      },
      i_aircraft: {
        required: true
      },
      o_flight: {
        required: function () {
          return !$('#outboundForm input, #outboundForm select').prop('disabled');
        }
      },
      o_from: {
        required: true,
        notEqualTo: '#o_to'
      },
      o_to: {
        required: true,
        notEqualTo: '#o_from'
      },
      o_departure_time: {
        required: true
      },
      o_arrival_time: {
        required: true
      },
      o_aircraft: {
        required: true
      },
      effective_date: {
        required: true
      },
      'day[]': {
        required: true
      }
    },
    messages: {
      i_from: {
        notEqualTo: 'From and To cannot be the same.'
      },
      i_to: {
        notEqualTo: 'From and To cannot be the same.'
      },
      o_from: {
        notEqualTo: 'From and To cannot be the same.'
      },
      o_to: {
        notEqualTo: 'From and To cannot be the same.'
      }
    },
    submitHandler: function (form) {
      if ($('#location').val().trim() === '') {
        $('#location-error').text('This field is required.');
        return false; // Prevent form submission
      } else {
        $('#location-error').text(''); // Clear error
        form.submit();
      }
    }
  });
});
