/**
 * Edit Group
 */

'use strict';

$(document).ready(function () {
  // Initialize jQuery Validation
  $('#editGroupForm').validate({
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
      name: {
        required: true
      },
      status: {
        required: true
      }
    },
    messages: {
      name: {
        required: 'Please enter group name'
      },
      status: {
        required: 'Please select status'
      }
    }
  });
});
