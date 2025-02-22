@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Customer Detail')

@section('page-script')

<script src="{{asset('assets/js/customer/email/add.js')}}"></script>
<script src="{{asset('assets/js/customer/email/delete.js')}}"></script>

<script type="text/javascript">
    const select2 = $('.select2');

    if (select2.length) {
    select2.each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select value',
        dropdownParent: $this.parent()
        });
    }).on('change', function() {
        $(this).valid();
    });
    }

    $('#customer-emails-table thead tr').clone(true).appendTo('#customer-emails-table thead');

    var dt = $('#customer-emails-table').DataTable({
        orderCellsTop: true,
        stateSave: true,
        order: [],
        dom:    '<"row align-items-center mb-2"' +
                    '<"col-sm-12 col-md-6 d-flex align-items-center" l>' +
                    '<"col-sm-12 col-md-6 d-flex justify-content-md-end gap-2"B f>' +
                '>' +
                '<"table-responsive table-bordered" t>' +
                '<"row mt-2"' +
                    '<"col-sm-12 col-md-6" i>' +
                    '<"col-sm-12 col-md-6 d-flex justify-content-md-end" p>' +
                '>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-info'            
            },
            {
                text: '<i class="fas fa-filter"></i> Reset',
                className: 'btn btn-secondary',
                action: function (e, dt, node, config) {
                    dt.search('').columns().search('').draw();
                    $('#customer-emails-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ],
        columnDefs: [
            {
                targets: [0], // Index of the "Actions" column
                searchable: false,
                orderable: false
            }
        ]
    });

    $('#customer-emails-table thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        if (![0].includes(i)) { // Exclude the "Actions" column from searching
          $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        } else {
          $(this).html('<input type="text" class="form-control" disabled />');
        }

        $('input', this).on('keyup change', function () {
            if (![0].includes(i) && dt.column(i).search() !== this.value) {
                dt.column(i).search(this.value).draw();
            }
        });
    });

    $(document).on('click', '.edit-customer-email-btn', function () {
      let customerEmailId = $(this).data('record-id');

      // Load the edit form content into the modal
      $.ajax({
          url: '{{ route("maintenance.customer.email.edit", [":customerId", ":recordId"]) }}'.replace(':customerId', {{ $customer->id }}).replace(':recordId', customerEmailId),
          type: 'GET',
          success: function (response) {
              $('#editCustomerEmail .modal-form').html(response);
              $('#editCustomerEmail').show();

              const select2ForEdit = $('.select2ForEdit');

                if (select2ForEdit.length) {
                    select2ForEdit.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Select value',
                    dropdownParent: $this.parent()
                    });
                }).on('change', function() {
                    $(this).valid();
                });
                }

              // Initialize jQuery Validation
              $('#editCustomerEmailForm').validate({
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
                        email: {
                            required: true,
                            email: true
                        },
                        status: {
                            required: true
                        }
                    }
                });
          }
      });
  });
</script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Customer Detail
</h4>

@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
@endif
@if(session('failure'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('failure') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible" role="alert">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
    </button>
</div>
@endif

<div class="card">
    <div class="card-body p-3 d-flex justify-content-between align-items-center">
        <div class="align-self-center">
            <span class="btn btn-md btn-primary"><strong>{{ $customer->name }}</strong></span>
        </div>
        <a href="{{ route('maintenance.customer.list') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-back me-0 me-sm-1 ti-xs"></i> Customer List
        </a>
    </div>
</div>

@can('view customers')
<div class="card mt-2">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Products</h5>
        @if($customer->active)
        @can('add customers')
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#manageProductsForCustomer">
            <span><i class="ti ti-tag me-0 me-sm-1 ti-xs"></i>Manage Products</span>
        </button>
        @endcan
        @endif
    </div>
    <div class="card-body p-3">
        <!-- Products List -->
        @if($customerProducts->count() > 0)
        <div class="d-flex flex-wrap gap-2">
            @foreach($customerProducts as $product)
            <button type="button" class="btn btn-outline-success">
                {{ $product->name }}
            </button>
            @endforeach
        </div>
        @else
        <p class="text-center text-muted">No products are assigned to this customer.</p>
        @endif
    </div>
</div>

<!-- Bordered Table -->
<div class="card mt-2">
    <div class="card-header p-3 d-flex justify-content-between">
        <h5>Emails</h5>
        @can('add customers')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerEmail">
            <span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>Add New Email</span>
        </button>
        @endcan
    </div>
    <div class="card-body p-3">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="customer-emails-table">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Locations</th>
                        <th>Status</th>
                        <th>Added By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customerEmails as $customerEmail)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
                                @can('edit customers')
                                <a href="javascript:;" class="text-body edit-customer-email-btn"
                                    data-record-id="{{ $customerEmail->id }}" data-bs-toggle="modal"
                                    data-bs-target="#editCustomerEmail"><i
                                        class="ti ti-edit ti-sm mx-2 text-info"></i></a>
                                @endcan
                                @can('delete customers')
                                <form
                                    action="{{ route('maintenance.customer.email.destroy', [$customer, $customerEmail]) }}"
                                    method="post" id="delete-customer-email-form-{{ $customerEmail->id }}">
                                    @csrf
                                    @method('delete')
                                    <a href="javascript:;"
                                        onclick="confirmCustomerEmailDelete({{ $customerEmail->id }})">
                                        <i class="ti ti-trash ti-sm mx-2 text-danger"></i>
                                    </a>
                                </form>
                                @endcan
                            </div>
                        </td>
                        <td>{{ $customerEmail->formatted_id }}</td>
                        <td>{{ $customerEmail->name }}</td>
                        <td>{{ $customerEmail->email }}</td>
                        <td>
                            @if ($customerEmail->locations->count() > 0)
                            @foreach ($customerEmail->locations as $location)
                            <span class="badge bg-label-primary me-1">{{ $location->name }}</span>
                            @endforeach
                            @else
                            <span class="badge bg-label-secondary me-1">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if ($customerEmail->active)
                            <span class="badge bg-label-success me-1">Active</span>
                            @else
                            <span class="badge bg-label-danger me-1">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $customerEmail->addedByUser->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->
@endcan

@include('_partials/_modals/customer/product')

@include('_partials/_modals/customer/email/add')

<!-- Edit Modal -->
<div class="modal" id="editCustomerEmail" tabindex="-1" aria-hidden="true" style="display: none">
    <div class="modal-dialog modal-lg modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Edit Customer Email</h3>
                </div>
                <div class="modal-form"></div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->

@endsection