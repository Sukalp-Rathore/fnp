@extends('layout')
@section('content')

<div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
    <div>
        <h1 class="page-title fw-medium fs-18 mb-0">Send Notifications Manually</h1>
    </div>
</div> 

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">All Customers</div>
                <div class="card-options">
                    <button class="btn btn-primary btn-sm send-mail">Send Mail</button>
                    <button class="btn btn-secondary btn-sm send-whatsapp">Whatsapp Notification</button>
                    <button class="btn btn-primary1 btn-sm send-sms">Send SMS</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="file-exports" class="table table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile No.</th>
                                <th>Type</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $c)
                                <tr>
                                    <td><input type="checkbox" class="form-check-input" value="{{ $c->id }}"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $c->customer_name }}</td>
                                    <td>{{ $c->customer_email }}</td>
                                    <td>{{ $c->customer_phone }}</td>
                                    <td>{{ ucfirst($c->customer_type) }}</td>
                                    <td>{{ $c->created_at ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection  


@section('js')

<script>
    $(document).ready(function() {
        var table = $('#file-exports').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'collection',
                text: '<i class="fa fa-download"></i> Export',
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fa fa-copy"></i> Copy'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv"></i> CSV'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel"></i> Excel'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf"></i> PDF'
                    }
                ]
            }],
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
            }
        });

        // Handle "Select All" checkbox
        $('#select-all').on('click', function() {
            var isChecked = $(this).is(':checked');
            $('input.form-check-input').prop('checked', isChecked);
        });

        // Ensure "Select All" checkbox updates when individual checkboxes are clicked
        $('#file-exports tbody').on('change', 'input.form-check-input', function() {
            if ($('input.form-check-input:checked').length === $('input.form-check-input').length) {
                $('#select-all').prop('checked', true);
            } else {
                $('#select-all').prop('checked', false);
            }
        });

        // Handle Send Mail button click
        $('.send-mail').on('click', function() {
            let selectedCustomers = [];
            $('input.form-check-input:checked').each(function() {
                selectedCustomers.push($(this).val());
            });

            if (selectedCustomers.length === 0) {
                alert('Please select at least one customer.');
                return;
            }

            $.ajax({
                url: "{{ route('send.mail') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    customer_ids: selectedCustomers
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while sending emails.');
                }
            });
        });

        // Handle Send Whatsapp button click
        $('.send-whatsapp').on('click', function() {
            let selectedCustomers = [];
            $('input.form-check-input:checked').each(function() {
                selectedCustomers.push($(this).val());
            });

            if (selectedCustomers.length === 0) {
                alert('Please select at least one customer.');
                return;
            }

            $.ajax({
                url: "{{ route('send.whatsapp') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    customer_ids: selectedCustomers
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while sending emails.');
                }
            });
        });

        // Handle Send SMS button click
        $('.send-sms').on('click', function() {
            let selectedCustomers = [];
            $('input.form-check-input:checked').each(function() {
                selectedCustomers.push($(this).val());
            });

            if (selectedCustomers.length === 0) {
                alert('Please select at least one customer.');
                return;
            }

            $.ajax({
                url: "{{ route('send.sms') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    customer_ids: selectedCustomers
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while sending emails.');
                }
            });
        });
    });
</script>
@endsection