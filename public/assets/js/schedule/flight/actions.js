/**
 * Delete Schedule Flight Remark
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  document.querySelectorAll('.mark-complete, .delete-flight, .cancel-flight').forEach(function (element) {
    element.addEventListener('click', function (event) {
      event.preventDefault(); // Prevent default action

      let url = this.getAttribute('href'); // Get the button's href

      // Customize message based on button clicked
      let actionText = 'perform this action';
      if (this.classList.contains('delete-flight')) {
        actionText = 'delete this flight';
      } else if (this.classList.contains('cancel-flight')) {
        actionText = 'cancel this flight';
      } else if (this.classList.contains('mark-complete')) {
        actionText = 'mark this flight as complete';
      }

      Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to ' + actionText + '?',
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
          window.location.href = url; // Redirect if confirmed
        }
      });
    });
  });
});
