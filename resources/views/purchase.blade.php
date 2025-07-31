@extends('layout')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">


    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <h1 class="page-title fw-medium fs-18 mb-0">Purchase Management</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">All Transactions</div>
                    <div class="card-options">
                        <a class="btn btn-success btn-sm editList">Edit Purchase Vendors</a>
                        <a class="btn btn-primary btn-sm openFormPurchase">Create Entry</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="file-exporttt" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Person Name</th>
                                    <th>Amount</th>
                                    <th>Amount Paid</th>
                                    <th>Amount Pending</th>
                                    <th>Payment Mode</th>
                                    <th>Payment Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $v)
                                    @php
                                        $pending = $v->amount - $v->paid_amount;
                                        if ($pending < 0) {
                                            $pending = 0;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $v->purchase_person }}</td>
                                        <td>{{ $v->amount }}</td>
                                        <td>{{ $v->paid_amount }}</td>
                                        <td>{{ $pending }}</td>
                                        <td>{{ $v->payment_mode }}</td>
                                        <td>{{ $v->payment_status }}</td>
                                        <td>{{ getutc($v->created_at, 'd.m.Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal to enter sale  --}}
    <div class="modal fade" id="enterPurchaseModal" tabindex="-1" aria-labelledby="exampleModalScrollable2"
        data-bs-keyboard="false" aria-hidden="true">
        <!-- Scrollable modal -->
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel2">Enter Todays Sales
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('purchase.enter') }}" method="POST" id="purchaseForm">
                    <div class="modal-body">
                        @csrf
                        <div class="row gy-3">
                            <div class="col-xl-12 form-group">
                                <label for="purchase_person" class="form-label text-default">Purchase Person Name</label>
                                <select class="sel form-control" name="purchase_person" id="purchase_person">
                                    <option value="" disabled selected>Select Purchase Person Name</option>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{ $vendor->name }}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-12 form-group">
                                <label for="amount" class="form-label text-default">Amount (Rs)</label>
                                <input type="number" class="form-control" id="amount" name="amount"
                                    placeholder="Enter Purchase Amount" autocomplete="off" required>
                            </div>
                            <div class="col-xl-12 form-group">
                                <label for="payment_mode" class="form-label text-default">Payment Mode</label>
                                <select class="sell form-control" name="payment_mode" id="payment_mode">
                                    <option value="" disabled selected>Select Payment Method</option>
                                    <option value="online">Online</option>
                                    <option value="cash">Cash</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div class="col-xl-12 form-group">
                                <label for="payment_status" class="form-label text-default">Payment Status</label>
                                <select class="sell form-control" name="payment_status" id="payment_status">
                                    <option value="" disabled selected>Select Payment Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="part-payment">Part Payment</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-xl-12 form-group">
                                <label for="paid_amount" class="form-label text-default">Amount Paid (Rs)</label>
                                <input type="number" class="form-control" id="paid_amount" name="paid_amount"
                                    placeholder="Enter Amount Paid" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <center>
                            <button type="submit" class="btn btn-primary">Create Entry</button>
                        </center>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end modal  --}}

    <!-- Modal for Editing Purchase Vendors -->
    <div class="modal fade" id="editVendorsModal" tabindex="-1" aria-labelledby="editVendorsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVendorsModalLabel">Manage Purchase Vendors</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Vendor Name</th>
                                <th>Total Purchase</th>
                                <th>Payment Pending</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="vendorsTable">
                            <!-- Vendors will be dynamically populated here -->
                        </tbody>
                    </table>
                    <form id="addVendorForm">
                        @csrf
                        <div class="row gy-3 mt-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="vendorName" name="name"
                                    placeholder="Enter Vendor Name" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Add Vendor</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- edit modal end  --}}

    <!-- Modal for Editing Vendor Name -->
    <div class="modal fade" id="editVendorNameModal" tabindex="-1" aria-labelledby="editVendorNameModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVendorNameModalLabel">Edit Vendor Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editVendorForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="editVendorId" name="id">
                        <div class="mb-3">
                            <label for="editVendorName" class="form-label">Vendor Name</label>
                            <input type="text" class="form-control" id="editVendorName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- name edit modal end   --}}
@endsection
@section('js')
    <!-- Custom JS -->
    <script src="assets/js/custom.js"></script>

    <!-- Select2 Cdn -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Internal Select-2.js -->
    <script src="assets/js/select2.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#file-exporttt').DataTable({
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

            $(document).on('click', '.openFormPurchase', function() {
                $("#enterPurchaseModal").modal('show');
            });

            $('#payment_mode').on('change', function() {
                if ($(this).val() === 'none') {
                    $('#payment_status').val('pending').trigger('change');
                    $('#paid_amount').val(0); // Reset paid amount to 0 if payment mode is 'none'
                }
            });

            $('#purchaseForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    },
                    error: function(response) {
                        if (response.responseJSON && response.responseJSON.error) {
                            toastr.error(response.responseJSON.error);
                        } else {
                            toastr.error("An unexpected error occurred.");
                        }
                    }
                });
            });

            // Open the Edit Vendors Modal
            $(document).on('click', '.editList', function() {
                fetchVendors();
                $('#editVendorsModal').modal('show');
            });

            // Fetch Vendors
            function fetchVendors() {
                $.ajax({
                    url: "{{ route('purchase-vendors.index') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let vendorsHtml = '';
                            response.vendors.forEach((vendor, index) => {
                                vendorsHtml += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${vendor.name}</td>
                                    <td>${vendor.total_purchase || 0}</td>
                                    <td>${vendor.amount_pending || 0}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning editVendorBtn" data-id="${vendor.id}" data-name="${vendor.name}">Edit</button>
                                        <button class="btn btn-sm btn-danger deleteVendorBtn" data-id="${vendor.id}">Delete</button>
                                    </td>
                                </tr>
                            `;
                            });
                            $('#vendorsTable').html(vendorsHtml);
                        }
                    },
                    error: function() {
                        toastr.error('Failed to fetch vendors.');
                    }
                });
            }

            // Add Vendor
            $('#addVendorForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('purchase-vendors.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            fetchVendors();
                            $('#vendorName').val('');
                        }
                    },
                    error: function() {
                        toastr.error('Failed to add vendor.');
                    }
                });
            });

            // Edit Vendor
            $(document).on('click', '.editVendorBtn', function() {
                const vendorId = $(this).data('id');
                const vendorName = $(this).data('name');

                // Populate the modal fields
                $('#editVendorId').val(vendorId);
                $('#editVendorName').val(vendorName);

                // Show the modal
                $('#editVendorNameModal').modal('show');
            });

            $('#editVendorForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                $.ajax({
                    url: "{{ route('purchase-vendors.update') }}",
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            fetchVendors(); // Refresh the vendor list
                            $('#editVendorNameModal').modal('hide'); // Close the modal
                        }
                    },
                    error: function() {
                        if (response.responseJSON && response.responseJSON.errors) {
                            toastr.error(response.responseJSON.errors.id[
                                0]); // Show the error message
                        } else {
                            toastr.error('Failed to update vendor.');
                        }
                    }
                });
            });

            // Delete Vendor
            $(document).on('click', '.deleteVendorBtn', function() {
                const vendorId = $(this).data('id');
                if (confirm('Are you sure you want to delete this vendor?')) {
                    $.ajax({
                        url: "{{ route('purchase-vendors.destroy') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: vendorId
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                fetchVendors();
                            }
                        },
                        error: function() {
                            toastr.error('Failed to delete vendor.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
