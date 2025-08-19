'use strict';

$(document).ready(function () {
  // Initially disable AWB inputs and form fields
  $('#newAwb, #subsequentAwb').prop('disabled', true);
  disableFormFields();
  clearAwbMessage();

  // Toggle which AWB input is enabled
  $('input[name="awb_type"]').on('change', function () {
    if ($(this).val() === 'new') {
      $('#newAwb').prop('disabled', false);
      $('#subsequentAwb').prop('disabled', true).val('');
    } else if ($(this).val() === 'subsequent') {
      $('#subsequentAwb').prop('disabled', false);
      $('#newAwb').prop('disabled', true).val('');
    }
    resetFormFields();
    clearAwbMessage();
  });

  // Check/Lookup AWB via AJAX
  $('.check-awb').on('click', function () {
    if (!$('#addScheduleFlightCustomerAwbForm').valid()) return;

    const awbType = $('input[name="awb_type"]:checked').val(); // 'new' | 'subsequent'
    const awbNumber = $('#newAwb').val() || $('#subsequentAwb').val();

    if (!awbType) {
      setAwbMessage('Please select an AWB type.', 'danger');
      return;
    }
    if (!awbNumber) {
      setAwbMessage('Please enter an AWB number.', 'danger');
      return;
    }

    $.ajax({
      url: '/checkAwbForScheduleFlightCustomer',
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      data: { awb_type: awbType, awb: awbNumber },
      success: function (response) {
        // Normalize server response
        const exists =
          response?.status === 'exists' ||
          response?.status === 'duplicate' ||
          response?.exists === true ||
          (Array.isArray(response?.records) && response.records.length > 0) ||
          !!response?.normal;

        const available =
          response?.status === 'available' || response?.status === 'new' || (!exists && response?.status === 'success');

        const normal =
          response?.normal ||
          (Array.isArray(response?.records) ? response.records.find(r => r.type === 'normal') : null);

        clearAwbMessage();

        // --- NEW / CONNECTING ---
        if (awbType === 'new') {
          if (exists) {
            // AWB already in system → treat as CONNECTING shipment (duplicate allowed across legs)
            setAwbMessage(
              `AWB ${awbNumber} already exists in the system. This entry will be treated as a <strong>Connecting shipment</strong>`,
              'success'
            );
            enableFormFields();
            // Pre-fill from master if available (same cargo details)
            if (normal) {
              prefillFromNormal(normal);
            }
            // Keep Actual Weight editable (some ops re-confirm leg weights)
            $('#actualWeight').val($('#actualWeight').val()); // no-op; just ensure enabled
          } else if (available) {
            // First time we see this AWB → it's a brand-new first leg
            setAwbMessage(
              `AWB ${awbNumber} available. It will be created as a <strong>new shipment</strong>.`,
              'success'
            );
            enableFormFields();
          } else {
            setAwbMessage('Unexpected response for New/Connecting AWB. Please try again.', 'danger');
            disableFormFields();
          }
        }

        // --- SUBSEQUENT (PARTIAL) ---
        if (awbType === 'subsequent') {
          if (exists) {
            setAwbMessage(
              `AWB ${awbNumber} found. You are entering a <strong>Subsequent (partial) shipment</strong>.`,
              'success'
            );
            enableFormFields();
            if (normal) {
              prefillFromNormal(normal);
              //console.log(normal);

              // If backend provides totals, hint remaining weight
              const declared = numberOrZero(normal.total_actual_weight);
              const loaded = numberOrZero(normal.actual_weight);
              const remaining = Math.max(declared - loaded, 0);
              if (declared > 0) {
                $('#actualWeight').attr(
                  'placeholder',
                  remaining > 0 ? `Suggested remaining: ${remaining}` : 'No remaining weight (0)'
                );
              }
              // Do NOT auto-fill actual; let user enter the partial for this leg
              $('#actualWeight').val('');
            }
          } else {
            setAwbMessage(
              `No existing record for AWB ${awbNumber}. Please add it as a <strong>New/Connecting</strong> shipment first.`,
              'danger'
            );
            disableFormFields();
          }
        }
      },
      error: function () {
        setAwbMessage('Error checking AWB. Please try again.', 'danger');
      }
    });
  });

  // jQuery Validation
  $('#addScheduleFlightCustomerAwbForm').validate({
    ignore: ':disabled', // ✅ ignore disabled inputs so they don't throw "required"
    errorElement: 'div',
    errorClass: 'invalid-feedback',
    highlight: function (element) {
      $(element).addClass('is-invalid').removeClass('is-valid');
    },
    unhighlight: function (element) {
      $(element).removeClass('is-invalid');
    },
    errorPlacement: function (error, element) {
      // place error under the field
      error.appendTo(element.parent().append('<div class="form-control-feedback"></div>'));
    },
    rules: {
      awb_type: { required: true },
      new_awb: {
        required: function () {
          return $('input[name="awb_type"]:checked').val() === 'new';
        },
        number: true,
        minlength: 11,
        maxlength: 11
      },
      subsequent_awb: {
        required: function () {
          return $('input[name="awb_type"]:checked').val() === 'subsequent';
        },
        number: true,
        minlength: 11,
        maxlength: 11
      },
      product: { required: true },
      destination: { required: true },
      declared_weight: { required: true },
      actual_weight: { required: true },
      total_actual_weight: { required: true }
    }
  });

  // ------- Helpers -------
  function enableFormFields() {
    $(
      '#product, #destination, #declaredWeight, #actualWeight, #volumetricWeight, #offloadedWeight, #totalVolumetricWeight, #totalActualWeight'
    ).prop('disabled', false);
  }

  function disableFormFields() {
    $(
      '#product, #destination, #declaredWeight, #actualWeight, #volumetricWeight, #offloadedWeight, #totalVolumetricWeight, #totalActualWeight'
    ).prop('disabled', true);
  }

  function resetFormFields() {
    disableFormFields();
    $('#product, #destination').val('').trigger('change');
    $('#declaredWeight, #actualWeight, #volumetricWeight, #offloadedWeight, #totalVolumetricWeight, #totalActualWeight')
      .val('')
      .attr('placeholder', '');
  }

  function setAwbMessage(html, tone) {
    // tone: 'success' | 'danger' | 'info' | 'warning'
    $('#awbMessage').removeClass('text-success text-danger text-info text-warning');
    $('#awbMessage')
      .addClass('text-' + tone)
      .html(html);
  }
  function clearAwbMessage() {
    $('#awbMessage').removeClass().empty();
  }

  function prefillFromNormal(normal) {
    // Safely prefill known fields if present
    if (normal.product_id != null) $('#product').val(normal.product_id).trigger('change');
    if (normal.destination != null) $('#destination').val(normal.destination).trigger('change');
    if (normal.declared_weight != null) $('#declaredWeight').val(normal.declared_weight);
    if (normal.volumetric_weight != null) $('#volumetricWeight').val(normal.volumetric_weight);
    if (normal.total_volumetric_weight != null) $('#totalVolumetricWeight').val(normal.total_volumetric_weight);
    if (normal.total_actual_weight != null) $('#totalActualWeight').val(normal.total_actual_weight);
  }

  function numberOrZero(v) {
    const n = parseFloat(v);
    return isNaN(n) ? 0 : n;
  }
});
