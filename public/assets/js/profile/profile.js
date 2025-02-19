/**
 * Account Settings - Profile & Security
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    // Update/reset user image of account page
    let accountUserImage = document.getElementById('uploadedAvatar');
    const fileInput = document.querySelector('.account-file-input'),
      resetFileInput = document.querySelector('.account-image-reset'),
      imageSubmitBtn = document.querySelector('.image-submit-btn');

    if (accountUserImage) {
      const resetImage = accountUserImage.src;
      fileInput.onchange = () => {
        if (fileInput.files[0]) {
          accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
          imageSubmitBtn.classList.remove('d-none');
        }
      };
      resetFileInput.onclick = () => {
        fileInput.value = '';
        accountUserImage.src = resetImage;
        imageSubmitBtn.classList.add('d-none');
      };
    }

    $.validator.addMethod(
      'passwordStrength',
      function (value, element) {
        return (
          this.optional(element) || /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}$/.test(value)
        );
      },
      'Password must contain at least one uppercase letter, one number, and one special character.'
    );

    // Initialize jQuery Validation
    $('#formAccountSettings').validate({
      errorElement: 'div',
      errorClass: 'invalid-feedback',
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid').removeClass('is-valid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      },
      errorPlacement: function (error, element) {
        // Add error message after the form-control-feedback div for other elements
        error.appendTo(element.parent().parent().append('<div class="form-control-feedback"></div>'));
      },
      rules: {
        currentPassword: {
          required: true
        },
        newPassword: {
          required: true,
          minlength: 8,
          passwordStrength: true
        },
        confirmPassword: {
          required: true,
          equalTo: '#newPassword'
        }
      },
      messages: {
        currentPassword: {
          required: 'Please enter current password'
        },
        newPassword: {
          required: 'Please enter new password',
          minlength: 'Password must be at least 8 characters long'
        },
        confirmPassword: {
          required: 'Please confirm new password',
          equalTo: 'New Password & Confirm Password do not match'
        }
      }
    });
  })();
});
