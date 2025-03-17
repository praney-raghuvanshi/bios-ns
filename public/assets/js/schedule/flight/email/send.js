/**
 * Send Schedule Flight Emails
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.send-email-btn').forEach(function (element) {
    element.addEventListener('click', function (event) {
      event.preventDefault(); // Prevent default action

      let baseUrl = this.getAttribute('href'); // Get base URL

      let params = new URLSearchParams();
      let sendEmailChecked = false;

      document.querySelectorAll('[name="include_remark[]"]:checked').forEach(function (checkbox) {
        params.append('include_remark[]', checkbox.value);
      });

      document.querySelectorAll('[name="send_email[]"]:checked').forEach(function (checkbox) {
        params.append('send_email[]', checkbox.value);
        sendEmailChecked = true; // At least one is checked
      });

      // If no send_email checkbox is checked, show an alert and return
      if (!sendEmailChecked) {
        Swal.fire({
          title: 'No Recipient Selected!',
          text: 'Please check at least one email recipient before proceeding.',
          icon: 'warning',
          confirmButtonText: 'OK',
          customClass: {
            confirmButton: 'btn btn-primary'
          }
        });
        return; // Stop further execution
      }

      let finalUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + params.toString();

      Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to proceed?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        customClass: {
          confirmButton: 'btn btn-primary me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(result => {
        if (result.isConfirmed) {
          window.location.href = finalUrl; // Redirect with updated parameters
        }
      });
    });
  });
});
