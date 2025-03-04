/**
 * Delete Flight Day
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  window.confirmFlightDayDelete = function (id) {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-2',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(result => {
      if (result.isConfirmed) {
        // Submit the form for deletion
        document.getElementById('delete-flight-day-form-' + id).submit();
      }
    });
  };
});
