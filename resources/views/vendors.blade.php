@extends('layout')
@section('content')
    <!-- jQuery Confirm CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/css/jquery-confirm.min.css">

    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <h1 class="page-title fw-medium fs-18 mb-0">All Vendors</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">Vendors</div>
                    <div class="card-options">
                        <a class="btn btn-primary btn-sm add-vendor">Add Vendor</a>
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
                                    <th>Alternate No.</th>
                                    <th>City</th>
                                    <th>Gender</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vendors as $v)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $v->first_name }}</td>
                                        <td>{{ $v->email }}</td>
                                        <td>{{ $v->mobile }}</td>
                                        <td>{{ $v->alternate_mobile ?? 'N/A' }}</td>
                                        <td>{{ $v->city }}</td>
                                        <td>{{ $v->gender }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm editBtn"
                                                data-id="{{ $v->_id }}">Edit</button>
                                            <button class="btn btn-info btn-sm detailsBtn"
                                                data-id="{{ $v->_id }}">Details</button>
                                            <button class="btn btn-danger btn-sm deleteBtn"
                                                data-id="{{ $v->_id }}">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modals for add vendor  --}}
    <div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="exampleModalLgLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLgLabel">Add Vendor</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="response">
                    <form id="addVendorForm" method="POST" action="{{ route('vendor.create') }}">
                        {{-- CSRF Token --}}
                        @csrf
                        <div class="mb-3">
                            <label for="first_name" class="form-label">Vendor Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                placeholder="Enter full vendor name" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter vendor email" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile No.</label>
                            <input type="tel" maxlength="10" class="form-control" id="mobile"
                                placeholder="Enter 10 digit mobile number" name="mobile" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="alternate_mobile" class="form-label">Alternate Mobile No.</label>
                            <input type="tel" maxlength="10" class="form-control" id="alternate_mobile"
                                placeholder="Enter 10 digit mobile number" name="alternate_mobile" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" placeholder="Enter city name"
                                name="city" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-secondary" type="submit">Add Vendor</button>
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
    <div class="modal fade" id="editVendorModal" tabindex="-1" aria-labelledby="exampleModalLgLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLgLabel">Update Vendor</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="responsed">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- modal end --}}
    {{-- modal to show vendor month order detaiils  --}}
    <div class="modal fade" id="vendorDetailsModal" tabindex="-1" aria-labelledby="vendorDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vendorDetailsModalLabel">Vendor Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Total Order Amount: <span id="totalOrderAmount">0</span></h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Customer Name (Primary)</th>
                                <th>Customer Name (Secondary)</th>
                                <th>Products</th>
                                <th>Order Price</th>
                            </tr>
                        </thead>
                        <tbody id="vendorOrdersTable">
                            <!-- Orders will be dynamically populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{--  end order details month --}}
@endsection

@section('js')
    <!-- jQuery Confirm JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/js/jquery-confirm.min.js"></script>

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

            $(document).on('click', '.detailsBtn', function() {
                const vendorId = $(this).data('id');

                // Open the modal
                $('#vendorDetailsModal').modal('show');

                // Clear previous data
                $('#vendorOrdersTable').empty();
                $('#totalOrderAmount').text('0');

                // Fetch vendor orders
                $.ajax({
                    url: "{{ route('vendor.orders') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        vendor_id: vendorId
                    },
                    success: function(response) {
                        if (response.success) {
                            const orders = response.orders;
                            const totalAmount = response.totalAmount;

                            // Check if there are no orders
                            if (orders.length === 0) {
                                $('#vendorDetailsModal').modal('hide'); // Close the modal
                                toastr.info('No orders for this vendor in the current month.');
                                return;
                            }


                            // Update total order amount
                            $('#totalOrderAmount').text(totalAmount);

                            // Populate the orders table
                            orders.forEach(order => {
                                $('#vendorOrdersTable').append(`
                                <tr>
                                    <td>${order.customer_name_primary || 'N/A'}</td>
                                    <td>${order.customer_name_secondary || 'N/A'}</td>
                                    <td>${order.products || 'N/A'}</td>
                                    <td>${order.order_price || 'N/A'}</td>
                                </tr>
                            `);
                            });
                        } else {
                            toastr.error('Failed to fetch vendor orders.');
                        }
                    },
                    error: function(xhr) {
                        console.error('An error occurred while fetching vendor orders.');
                    }
                });
            });

            $(document).on('click', '.add-vendor', function() {
                $("#addVendorModal").modal('show');
            });

            $('#addVendorForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#addVendorModal').modal('hide');
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
                let vendorId = $(this).attr('data-id');
                $("#editVendorModal").modal('show');
                $.ajax({
                    url: "{{ route('vendor.show.edit') }}",
                    type: 'POST',
                    data: {
                        vendorId: vendorId,
                    },
                    success: function(response) {
                        if (response.success == false) {
                            alert(response.message);
                        } else {
                            $("#responsed").html(response);
                        };
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
                    }
                });
            });


            $(document).on('click', '.deleteBtn', function() {
                let vendorId = $(this).attr('data-id');

                $.confirm({
                    title: 'Confirm Deletion',
                    content: 'Are you sure you want to delete this vendor?',
                    buttons: {
                        confirm: {
                            text: 'Yes, Delete',
                            btnClass: 'btn-red',
                            action: function() {
                                $.ajax({
                                    url: "{{ route('vendor.delete') }}",
                                    type: 'POST',
                                    data: {
                                        vendorId: vendorId,
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
