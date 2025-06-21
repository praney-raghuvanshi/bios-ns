/**
 * Daily Flight Report
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
  $('#dailyFlightReportForm').validate({
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
      flight_date: {
        required: true
      }
    }
  });

  //   let startWeekDropdown = $('#startWeek');
  //   let endWeekDropdown = $('#endWeek');
  //   let zoneDropdown = $('#zone');
  //   let customerDropdown = $('#customer');
  //   let flightDropdown = $('#flight');
  //   let fprRunBtn = $('#fprRunBtn');
  //   let allWeeks = [];

  //   $('#operationalYear').on('change', function () {
  //     let yearId = $(this).val();

  //     // Reset & Disable start week dropdown
  //     startWeekDropdown.empty().append('<option value="">-- Select Start Week --</option>').prop('disabled', true);
  //     endWeekDropdown.empty().append('<option value="">-- Select End Week --</option>').prop('disabled', true);
  //     zoneDropdown.val('').prop('disabled', true).trigger('change');
  //     customerDropdown.val('').prop('disabled', true).trigger('change');
  //     flightDropdown.empty().append('<option value="">-- Select Flight--</option>').prop('disabled', true);
  //     fprRunBtn.prop('disabled', true);

  //     if (yearId) {
  //       $.ajax({
  //         url: '/getWeeksForOperationalYear', // Update with your actual route
  //         type: 'POST',
  //         headers: {
  //           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token in request headers
  //         },
  //         data: { operational_year: yearId },
  //         success: function (response) {
  //           if (response.data.length > 0) {
  //             allWeeks = response.data; // Store all weeks for later use
  //             $.each(response.data, function (index, week) {
  //               let weekText = 'Week ' + week.week + ' (' + week.start_date + ' to ' + week.end_date + ')';
  //               startWeekDropdown.append('<option value="' + week.week + '">' + weekText + '</option>');
  //             });
  //             startWeekDropdown.prop('disabled', false);
  //           } else {
  //             startWeekDropdown.append('<option disabled selected> -- No Results -- </option>');
  //           }
  //         },
  //         error: function () {
  //           alert('Error fetching start weeks. Please try again.');
  //         }
  //       });
  //     }
  //   });

  //   // Update End Week dropdown when Start Week is selected
  //   startWeekDropdown.on('change', function () {
  //     let selectedStartWeek = parseInt($(this).val());
  //     endWeekDropdown.empty().append('<option value="">-- Select End Week --</option>').prop('disabled', true);
  //     zoneDropdown.val('').prop('disabled', true).trigger('change');
  //     customerDropdown.val('').prop('disabled', true).trigger('change');
  //     flightDropdown.empty().append('<option value="">-- Select Flight--</option>').prop('disabled', true);
  //     fprRunBtn.prop('disabled', true);

  //     if (allWeeks.length === 0) {
  //       let yearId = $('#operationalYear').val();

  //       if (yearId) {
  //         $.ajax({
  //           url: '/getWeeksForOperationalYear', // Update with your actual route
  //           type: 'POST',
  //           headers: {
  //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token in request headers
  //           },
  //           data: { operational_year: yearId },
  //           success: function (response) {
  //             if (response.data.length > 0) {
  //               allWeeks = response.data; // Store all weeks for later use
  //               if (selectedStartWeek) {
  //                 $.each(allWeeks, function (index, week) {
  //                   if (week.week >= selectedStartWeek) {
  //                     // Only include weeks on or after the selected start week
  //                     let weekText = 'Week ' + week.week + ' (' + week.start_date + ' to ' + week.end_date + ')';
  //                     endWeekDropdown.append('<option value="' + week.week + '">' + weekText + '</option>');
  //                   }
  //                 });
  //                 endWeekDropdown.prop('disabled', false);
  //               }
  //             }
  //           },
  //           error: function () {
  //             alert('Error fetching weeks. Please try again.');
  //           }
  //         });
  //       }
  //     } else {
  //       if (selectedStartWeek) {
  //         $.each(allWeeks, function (index, week) {
  //           if (week.week >= selectedStartWeek) {
  //             // Only include weeks on or after the selected start week
  //             let weekText = 'Week ' + week.week + ' (' + week.start_date + ' to ' + week.end_date + ')';
  //             endWeekDropdown.append('<option value="' + week.week + '">' + weekText + '</option>');
  //           }
  //         });
  //         endWeekDropdown.prop('disabled', false);
  //       }
  //     }
  //   });

  //   // Update Zone dropdown when End Week is selected
  //   endWeekDropdown.on('change', function () {
  //     zoneDropdown.prop('disabled', false);
  //     customerDropdown.val('').prop('disabled', true).trigger('change');
  //     flightDropdown.empty().append('<option value="">-- Select Flight --</option>').prop('disabled', true);
  //     fprRunBtn.prop('disabled', true);
  //   });

  //   // Update Customer dropdown when Zone is selected
  //   zoneDropdown.on('change', function () {
  //     customerDropdown.prop('disabled', false);
  //     flightDropdown.empty().append('<option value="">-- Select Flight --</option>').prop('disabled', true);
  //     fprRunBtn.prop('disabled', true);
  //   });

  //   // Update Flight dropdown when Customer is selected
  //   customerDropdown.on('change', function () {
  //     let customerId = $(this).val();
  //     flightDropdown.empty().append('<option value="">-- Select Flight --</option>').prop('disabled', true);
  //     fprRunBtn.prop('disabled', true);

  //     if (customerId) {
  //       $.ajax({
  //         url: '/getFlightsForCustomer', // Update with your actual route
  //         type: 'POST',
  //         headers: {
  //           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token in request headers
  //         },
  //         data: { customer: customerId },
  //         success: function (response) {
  //           if (response.data.length > 0) {
  //             flightDropdown.append('<option value="all">All Flights</option>');
  //             $.each(response.data, function (index, flightNumber) {
  //               flightDropdown.append('<option value="' + flightNumber + '">' + flightNumber + '</option>');
  //             });
  //             flightDropdown.prop('disabled', false);
  //           } else {
  //             flightDropdown.append('<option disabled selected> -- No Results -- </option>');
  //           }
  //         },
  //         error: function () {
  //           alert('Error fetching flights. Please try again.');
  //         }
  //       });
  //     }
  //   });

  //   // Enable Run Report button when flight is selected
  //   flightDropdown.on('change', function () {
  //     fprRunBtn.prop('disabled', false);
  //   });
});
