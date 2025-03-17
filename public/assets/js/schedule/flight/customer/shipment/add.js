/**
 * Add Schedule Flight Customer AWB
 */

'use strict';

$(document).ready(function () {
  // Initially disable fields
  $('#newAwb, #subsequentAwb').prop('disabled', true);

  // Enable only the selected AWB field
  $('input[name="awb_type"]').on('change', function () {
    if ($(this).val() === 'new') {
      $('#newAwb').prop('disabled', false);
      $('#subsequentAwb').prop('disabled', true).val('');
    } else if ($(this).val() === 'subsequent') {
      $('#subsequentAwb').prop('disabled', false);
      $('#newAwb').prop('disabled', true).val('');
    }
  });

  // Validate AWB existence via AJAX
  $('.check-awb').on('click', function () {
    if ($('#addScheduleFlightCustomerAwbForm').valid()) {
      let awbType = $('#newAwbRadio').val() || $('#subsequentAwbRadio').val();
      let awbNumber = $('#newAwb').val() || $('#subsequentAwb').val();

      if (!awbNumber) {
        $('#awbMessage').html('<div class="text-danger">Please enter an AWB number.</div>');
        return;
      }

      $.ajax({
        url: '/checkAwbForScheduleFlightCustomer',
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { awb_type: awbType, awb: awbNumber },
        success: function (response) {
          console.log(response);

          if (response.success) {
            if (response.show_error) {
              $('#awbMessage').html('<div class="text-danger">AWB already exists!</div>');
            } else {
              $('#awbMessage').html('<div class="text-success">AWB is available.</div>');
              $('#product').prop('disabled', false);
              $('#destination').prop('disabled', false);
              $('#declaredWeight').prop('disabled', false);
              $('#actualWeight').prop('disabled', false);
              $('#volumetricWeight').prop('disabled', false);
              $('#offloadedWeight').prop('disabled', false);
              $('#totalVolumetricWeight').prop('disabled', false);
              $('#totalActualWeight').prop('disabled', false);
            }
          } else {
            $('#awbMessage').html('<div class="text-danger">Error checking AWB.</div>');
          }
        },
        error: function () {
          $('#awbMessage').html('<div class="text-danger">Error checking AWB.</div>');
        }
      });
    }
  });

  // Initialize jQuery Validation
  $('#addScheduleFlightCustomerAwbForm').validate({
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
      awb_type: {
        required: true
      },
      new_awb: {
        required: true,
        number: true,
        minlength: 11,
        maxlength: 11
      },
      subsequent_awb: {
        required: true,
        number: true,
        minlength: 11,
        maxlength: 11
      },
      product: {
        required: true
      },
      destination: {
        required: true
      },
      declared_weight: {
        required: true
      },
      actual_weight: {
        required: true
      },
      total_actual_weight: {
        required: true
      }
    }
  });
});
