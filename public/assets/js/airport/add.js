/**
 * Add Airport
 */

'use strict';

$(document).ready(function () {
  const select2 = $('.select2');

  if (select2.length) {
    select2
      .each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>').select2({
          placeholder: 'Select value',
          dropdownParent: $this.parent()
        });
      })
      .on('change', function () {
        $(this).valid();
      });
  }

  // Initialize jQuery Validation
  $('#addAirportForm').validate({
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
      iata: {
        required: true,
        minlength: 3,
        maxlength: 3
      },
      name: {
        required: true
      },
      city: {
        required: true
      },
      country: {
        required: true
      },
      summer_difference: {
        required: true,
        number: true
      },
      winter_difference: {
        required: true,
        number: true
      },
      status: {
        required: true
      }
    }
  });
});
