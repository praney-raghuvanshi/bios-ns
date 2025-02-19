/**
 * Add Group
 */

'use strict';

$(document).ready(function () {
  // Initialize jQuery Validation
  $('#addGroupForm').validate({
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
      group_name: {
        required: true
      }
    },
    messages: {
      group_name: {
        required: 'Please enter unique group name'
      }
    }
  });
});
