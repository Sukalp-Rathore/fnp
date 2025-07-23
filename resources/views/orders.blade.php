@extends('layout')
@section('content')
    <!-- jQuery Confirm CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/css/jquery-confirm.min.css">

    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <h1 class="page-title fw-medium fs-18 mb-0">All Orders</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">Orders</div>
                    <div class="card-options">
                        <a class="btn btn-primary btn-sm add-order">Create New Order</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="file-exports" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Order Date</th>
                                    <th>Created By</th>
                                    <th>Primary Customer</th>
                                    <th>Secondary Customer</th>
                                    <th>Mobile No.(Primary)</th>
                                    <th>Mobile No.(Secondary)</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>Email(Primary)</th>
                                    <th>Email(Secondary)</th>
                                    <th>Event Name</th>
                                    <th>Products</th>
                                    <th>Vendor</th>
                                    <th>Delivery Date</th>
                                    <th>Order Status</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $o)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ getutc($o->created_at, 'd.m.Y') }}</td>
                                        <td>{{ $o->created_by }}</td>
                                        <td>{{ $o->customer_name_primary }}</td>
                                        <td>{{ $o->customer_name_secondary }}</td>
                                        <td>{{ $o->customer_mobile_primary }}</td>
                                        <td>{{ $o->customer_mobile_secondary }}</td>
                                        <td>{{ $o->customer_address }}</td>
                                        <td>{{ $o->city }}</td>
                                        <td>{{ $o->customer_email_primary }}</td>
                                        <td>{{ $o->customer_email_secondary }}</td>
                                        <td>{{ $o->event_name }}</td>
                                        <td>{{ $o->products }}</td>
                                        <td>{{ $o->vendor }}</td>
                                        <td>{{ getutc($o->delivery_date, 'd.m.Y') }}</td>
                                        <td>
                                            @if ($o->order_status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif ($o->order_status == 'delivered')
                                                <span class="badge bg-success">Delivered</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($o->order_status == 'pending')
                                                <button class="btn btn-success markDelivered"
                                                    data-id="{{ $o->_id }}">Mark Delivered</button>
                                            @endif
                                            {{-- <button class="btn btn-secondary editBtn">Edit</button> --}}
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
    <div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="exampleModalLgLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLgLabel">Create New Order</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="response">
                    <form id="createOrderForm">
                        @csrf
                        <input type="hidden" name="created_by" value="manual">
                        <!-- Order Type -->
                        <div class="mb-3">
                            <label for="orderType" class="form-label">Order Type</label>
                            <select class="form-select" id="orderType" name="order_type" required>
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                            </select>
                        </div>

                        <!-- Primary Order Fields -->
                        <div id="primaryOrderFields">
                            <div class="mb-3">
                                <label for="customerNamePrimary" class="form-label">Customer Name (Primary)</label>
                                <input type="text" class="form-control" id="customerNamePrimary"
                                    name="customer_name_primary" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="customerEmailPrimary" class="form-label">Customer Email (Primary)</label>
                                <input type="email" class="form-control" id="customerEmailPrimary"
                                    name="customer_email_primary" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="customerMobilePrimary" class="form-label">Customer Mobile (Primary)</label>
                                <input type="tel" maxlength="10" class="form-control" id="customerMobilePrimary"
                                    name="customer_mobile_primary" required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="customerAddress" class="form-label">Customer Address</label>
                                <textarea class="form-control" id="customerAddress" name="customer_address" rows="2" required autocomplete="off"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" required
                                    autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="eventName" class="form-label">Event Name</label>
                                <select class="form-select" id="eventName" name="event_name" required autocomplete="off">
                                    <option value="" selected>Select Event</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event }}">{{ $event }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="event_date" class="form-label">Event Date</label>
                                <input type="date" class="form-control" id="event_date" name="event_date" required
                                    autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="deliveryDate" class="form-label">Delivery Date</label>
                                <input type="date" class="form-control" id="deliveryDate" name="delivery_date"
                                    required autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="products" class="form-label">Products</label>
                                <textarea class="form-control" id="products" name="products" rows="3" required autocomplete="off"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="order_price" class="form-label">Order Value (Rupees)</label>
                                <input type="text" class="form-control" id="order_price" name="order_price" required
                                    autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="vendorCheckbox" class="form-label">Select Vendor?</label>
                                <input type="checkbox" id="vendorCheckbox" name="vendor_checkbox">
                            </div>
                            <div id="vendorSelection" class="mb-3" style="display: none;">
                                <label for="vendor" class="form-label">Vendor</label>
                                <select class="form-select" id="vendor" name="vendor">
                                    <option value="" selected>Select Vendor</option>
                                </select>
                            </div>
                        </div>

                        <!-- Secondary Order Fields -->
                        <div id="secondaryOrderFields" style="display: none;">
                            <div class="mb-3">
                                <label for="customerNameSecondary" class="form-label">Customer Name (Secondary)</label>
                                <input type="text" class="form-control" id="customerNameSecondary"
                                    name="customer_name_secondary" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="customerEmailSecondary" class="form-label">Customer Email (Secondary)</label>
                                <input type="email" class="form-control" id="customerEmailSecondary"
                                    name="customer_email_secondary" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="customerMobileSecondary" class="form-label">Customer Mobile
                                    (Secondary)</label>
                                <input type="tel" maxlength="10" class="form-control" id="customerMobileSecondary"
                                    name="customer_mobile_secondary" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="customer_address_secondary" class="form-label">Customer Address</label>
                                <textarea class="form-control" id="customerAddressSecondary" name="customer_address_secondary" rows="2"
                                    autocomplete="off"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Order</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
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

            $(document).on('click', '.add-order', function() {
                $("#addOrderModal").modal('show');
            });

            // Fetch vendors when the city input changes
            $('#city').on('input', function() {
                const city = $(this).val();

                if (city.length > 0) {
                    $.ajax({
                        url: "{{ route('get.vendors.by.city') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            city: city
                        },
                        success: function(response) {
                            if (response.success) {
                                const vendors = response.vendors;
                                const vendorDropdown = $('#vendor');

                                // Clear the existing options
                                vendorDropdown.empty();
                                vendorDropdown.append(
                                    '<option value="" selected>Select Vendor</option>');

                                // Populate the dropdown with the fetched vendors
                                vendors.forEach(function(vendor) {
                                    vendorDropdown.append(
                                        `<option value="${vendor._id}">${vendor.first_name}</option>`
                                    );
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('An error occurred while fetching vendors.');
                        }
                    });
                }
            });

            $('#orderType').on('change', function() {
                const orderType = $(this).val();
                if (orderType === 'primary') {
                    $('#primaryOrderFields').show();
                    $('#secondaryOrderFields').hide();
                } else if (orderType === 'secondary') {
                    $('#primaryOrderFields').show();
                    $('#secondaryOrderFields').show();
                }
            });

            // Toggle vendor selection
            $('#vendorCheckbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#vendorSelection').show();
                } else {
                    $('#vendorSelection').hide();
                }
            });

            // Handle form submission
            $('#createOrderForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('order.create') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#createOrderModal').modal('hide');
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while creating the order.');
                    }
                });
            });

            $(document).on('click', '.markDelivered', function() {
                var orderId = $(this).data('id');

                $.confirm({
                    title: 'Confirm Action',
                    content: 'Are you sure you want to mark this order as delivered?',
                    buttons: {
                        confirm: {
                            text: 'Yes',
                            btnClass: 'btn-success',
                            action: function() {
                                $.ajax({
                                    url: "{{ route('order.mark.delivered') }}",
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        order_id: orderId
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            toastr.success(response.message);
                                            location.reload();
                                        } else {
                                            toastr.error(response.message);
                                        }
                                    },
                                    error: function(xhr) {
                                        toastr.error(
                                            'An error occurred while marking the order as delivered.'
                                        );
                                    }
                                });
                            }
                        },
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-danger',
                            action: function() {
                                toastr.info('Action canceled.');
                            }
                        }
                    }
                });
            });

        });
    </script>
@endsection
