@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'BIOS Audit List')

@section('page-script')
<script type="text/javascript">
    $(document).ready(function () {
    // Setup - add a text input to each header cell
    $('#bios-audit-table thead tr').clone(true).appendTo('#bios-audit-table thead');

    var dt = $('#bios-audit-table').DataTable({
      orderCellsTop: true,
        stateSave: true,
        order: [[0, 'desc']],
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
                    $('#bios-audit-table thead tr:eq(1) th input').val('');
                    dt.draw();
                }
            }
        ]
    });

    $('#bios-audit-table thead tr:eq(1) th').each(function (i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

        $('input', this).on('keyup change', function () {
            dt.column(i).search(this.value).draw();
        });
    });
});
</script>
@endsection

@section('content')
<h4 class="mb-4">
    <span class="text-muted fw-light">BIOS /</span> Audit
</h4>

<!-- Bordered Table -->
<div class="card">
    <h5 class="card-header">BIOS Audit</h5>
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered text-center" id="bios-audit-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Log Name</th>
                        <th>Description</th>
                        {{-- <th>Subject Type</th> --}}
                        {{-- <th>Event</th> --}}
                        <th>For</th>
                        {{-- <th>Causer Type</th> --}}
                        <th>By</th>
                        <th>Properties</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td><span class="badge bg-primary">{{ $log->log_name }}</span></td>
                        <td>{{ $log->description }}</td>
                        {{-- <td>{{ $log->subject_type }}</td> --}}
                        {{-- <td>{{ $log->event }}</td> --}}
                        <td>
                            @if($log->subject)
                            #{{ $log->subject_id }} - {{ $log->subject->name }}
                            @else
                            <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        {{-- <td>{{ $log->causer_type }}</td> --}}
                        <td>
                            @if($log->causer)
                            <i class="fas fa-user"></i> {{ $log->causer->name }}
                            @else
                            <span class="text-muted">System</span>
                            @endif
                        </td>
                        <td>
                            @php
                            $logData = json_decode($log->properties, true);
                            @endphp
                            @if(isset($logData['attributes']))
                            <ul class="list-unstyled mb-0">
                                @foreach ($logData['attributes'] as $key => $newValue)
                                @if (!is_numeric($key))
                                <li>
                                    <strong class="text-dark">{{ ucwords(str_replace('_', ' ', $key)) }}:</strong>
                                    @isset($logData['old'][$key])
                                    <span class="text-danger"><del>{{ is_array($logData['old'][$key]) ?
                                            json_encode($logData['old'][$key]) : $logData['old'][$key] }}</del></span>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endisset
                                    →
                                    <span class="text-success fw-bold">
                                        @if (is_array($newValue))
                                        @foreach ($newValue as $newKey => $datum)
                                        @if (is_array($datum))
                                        @foreach($datum as $newKey2 => $datum2)
                                        {{ ucfirst($newKey2) }}: {{ $datum2 }}<br>
                                        @endforeach
                                        @else
                                        {{ ucfirst($newKey) }}: {{ $datum }}<br>
                                        @endif
                                        @endforeach
                                        @else
                                        {{ $newValue }}
                                        @endif
                                    </span>
                                </li>
                                @endif
                                @endforeach
                            </ul>
                            @else
                            {{-- Case 2: Manual log entry (without attributes) --}}
                            <div>
                                @isset($logData['old'])
                                <span class="text-danger">
                                    <del>{!! json_encode($logData['old'], JSON_PRETTY_PRINT) !!}</del>
                                </span>
                                <br>
                                @endisset

                                @isset($logData['new'])
                                <span class="text-success">
                                    {!! json_encode($logData['new'], JSON_PRETTY_PRINT) !!}
                                </span>
                                @endisset
                            </div>
                            @endif
                        </td>
                        <td>
                            {{ Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--/ Bordered Table -->

@endsection