@extends('layout')
@section('content')
    <!-- jQuery Confirm CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/css/jquery-confirm.min.css">

    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <h1 class="page-title fw-medium fs-18 mb-0">All Customers</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">Primary and Secondary Customers</div>
                    <div class="card-options">
                        <a class="btn btn-primary btn-sm add-customer">Add Customer</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="file-exports" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile No.</th>
                                    <th>Address</th>
                                    <th>Event</th>
                                    <th>Event Date</th>
                                    <th>Type</th>
                                    <th>Created At</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals for add vendor  --}}
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="exampleModalLgLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLgLabel">Add Customer</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="response">
                    <form id="addCustomerForm" method="POST" action="{{ route('customer.create') }}">
                        {{-- CSRF Token --}}
                        @csrf
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                placeholder="Enter full customer name" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Customer Email</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email"
                                placeholder="Enter customer email" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Mobile No.</label>
                            <input type="tel" maxlength="10" class="form-control" id="customer_phone"
                                placeholder="Enter 10 digit customer phone number" name="customer_phone" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="customer_address" class="form-label">Customer address</label>
                            <input type="text" class="form-control" id="customer_address"
                                placeholder="Enter customer address" name="customer_address" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="customer_type" class="form-label">Customer Type</label>
                            <select class="form-select" id="customer_type" name="customer_type">
                                <option value="Primary">Primary</option>
                                <option value="Secondary">Secondary</option>
                            </select>
                        </div>
                        <div class="mb-3" id="primaryCustomerField" style="display: none;">
                            <label for="primary_customer_name" class="form-label">Primary Customer Name</label>
                            <input type="text" class="form-control" id="primary_customer_name"
                                name="primary_customer_name" placeholder="Enter primary customer name" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="event_name" class="form-label">Choose event</label>
                            <select class="form-select" id="event_name" name="event_name">
                                @foreach ($events as $event)
                                    <option value="{{ $event }}">{{ $event }}</option>
                                @endforeach
                                <option value="">None</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="event_date">Event Date</label>
                            <input type="date" class="form-control" id="event_date" name="event_date"
                                placeholder="Select event date" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-secondary" type="submit">Add Customer</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modals for edit vendor  --}}
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="exampleModalLgLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLgLabel">Update Customer</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="responsec">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- modal end --}}
@endsection


@section('js')
    <!-- jQuery Confirm JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/js/jquery-confirm.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#customer_type').on('change', function() {
                if ($(this).val() === 'Secondary') {
                    $('#primaryCustomerField').show();
                } else {
                    $('#primaryCustomerField').hide();
                }
            });

            var table = $('#file-exports').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('customers') }}",
                    type: "GET",
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            // Calculate S.No based on current page and records per page
                            return meta.row + 1 + meta.settings._iDisplayStart;
                        },
                        orderable: false,
                        searchable: false
                    }, // S.No
                    {
                        data: 'customer_name',
                        name: 'customer_name',
                        render: function(data) {
                            return data ? data : 'N/A'; // Handle null values
                        }
                    }, // Name
                    {
                        data: 'customer_email',
                        name: 'customer_email',
                        render: function(data) {
                            return data ? data : 'N/A'; // Handle null values
                        }
                    }, // Email
                    {
                        data: 'customer_phone',
                        name: 'customer_phone',
                        render: function(data) {
                            return data ? data : 'N/A'; // Handle null values
                        }
                    }, // Mobile No.
                    {
                        data: 'customer_address',
                        name: 'customer_address',
                        render: function(data) {
                            return data ? data : 'N/A'; // Handle null values
                        }
                    }, // Address
                    {
                        data: 'event_name',
                        name: 'event_name',
                        render: function(data) {
                            return data ? data : 'N/A'; // Handle null values
                        }
                    }, // Event
                    {
                        data: 'event_date',
                        name: 'event_date',
                        render: function(data) {
                            return data ? data : 'N/A'; // Handle null values
                        }
                    }, // Event Date
                    {
                        data: 'customer_type',
                        name: 'customer_type',
                        render: function(data) {
                            return data ? data : 'N/A'; // Handle null values
                        }
                    }, // Type
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data ? data : 'N/A'; // Handle null values
                        }
                    }, // Created At
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                        <button class="btn btn-primary btn-sm editCustomer editBtn" data-id="${row.id}">Edit</button>
                        <button class="btn btn-danger btn-sm deleteCustomer deleteBtn" data-id="${row.id}">Delete</button>
                    `;
                        },
                        orderable: false,
                        searchable: false
                    } // Options
                ],
                order: [
                    [0, 'asc']
                ], // Default sorting by S.No
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

            $(document).on('click', '.add-customer', function() {
                $("#addCustomerModal").modal('show');
            });

            $('#addCustomerForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#addCustomerModal').modal('hide');
                        window.location.reload();
                        toastr.success(response.message);
                    },
                    error: function(xhr, status, error) {
                        var response = xhr.responseJSON;
                        if (response && response.errors) {
                            $.each(response.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.editBtn', function() {
                let customerId = $(this).attr('data-id');
                $("#editCustomerModal").modal('show');
                $.ajax({
                    url: "{{ route('customer.show.edit') }}",
                    type: 'POST',
                    data: {
                        customerId: customerId,
                    },
                    success: function(response) {
                        if (response.success == false) {
                            alert(response.message);
                        } else {
                            $("#responsec").html(response);
                        };
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                let customerId = $(this).attr('data-id');

                $.confirm({
                    title: 'Confirm Deletion',
                    content: 'Are you sure you want to delete this customer?',
                    buttons: {
                        confirm: {
                            text: 'Yes, Delete',
                            btnClass: 'btn-red',
                            action: function() {
                                $.ajax({
                                    url: "{{ route('customer.delete') }}",
                                    type: 'POST',
                                    data: {
                                        customerId: customerId,
                                        _token: '{{ csrf_token() }}' // add CSRF token manually if needed
                                    },
                                    success: function(response) {
                                        if (response.success == false) {
                                            $.alert({
                                                title: 'Error',
                                                content: response.message,
                                                type: 'red'
                                            });
                                        } else {
                                            toastr.success(response.message);
                                            setTimeout(function() {
                                                window.location.reload();
                                            }, 1000);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error(xhr.responseText);
                                        $.alert({
                                            title: 'Ajax Error',
                                            content: 'Something went wrong while deleting.',
                                            type: 'red'
                                        });
                                    }
                                });
                            }
                        },
                        cancel: function() {
                            // Do nothing on cancel
                        }
                    }
                });
            });

        });
    </script>
@endsection
