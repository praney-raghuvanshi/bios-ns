/**
 * Daily Report
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

  $('#zone').on('change', function () {
    let selected = $(this).val();

    // if "all" is selected, clear others
    if (selected && selected.includes('all')) {
      $(this).val(['all']).trigger('change');
    }
  });

  $('#flight').on('change', function () {
    let selected = $(this).val();

    // if "all" is selected, clear others
    if (selected && selected.includes('all')) {
      $(this).val(['all']).trigger('change');
    }
  });

  // Initialize jQuery Validation
  $('#dailyReportForm').validate({
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
      start_date: {
        required: true
      },
      end_date: {
        required: true
      },
      zone: {
        required: true
      },
      flight: {
        required: true
      }
    }
  });
});
