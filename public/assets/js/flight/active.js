/**
 * Active Flight Day
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  document.querySelectorAll('.make-active-btn').forEach(button => {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Make it Active!',
        customClass: {
          confirmButton: 'btn btn-primary me-2',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
      }).then(result => {
        if (result.isConfirmed) {
          let href = this.href;
          //console.log(href);
          window.location.href = href;
        }
      });
    });
  });
});
