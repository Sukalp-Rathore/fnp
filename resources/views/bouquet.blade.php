@extends('layout')
@section('content')

<div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
    <div>
        <h1 class="page-title fw-medium fs-18 mb-0">Bouquet Mangement</h1>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card custom-card">
            <div class="card-header justify-content-between">
                <div class="card-title">All Bouqets</div>
                <div class="card-options">
                    <a class="btn btn-primary btn-sm create-bouquet">Create Bouquet</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="file-exports" class="table table-bordered text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Customer Name</th>
                                <th>Bouquet Price</th>
                                <th>Created At</th>
                                <th>Delivery Date</th>
                                <th>Delivery Address</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bouquets as $b)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $b->customer_name }}</td>
                                    <td>{{ $b->total_price }}</td>
                                    <td>{{ $b->created_at }}</td>
                                    <td>{{ $b->delivery_date }}</td>
                                    <td>{{ $b->delivery_address ?? '' }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm infoBtn" data-id="{{$b->_id}}">Info</button>
                                        <button class="btn btn-secondary btn-sm editBtn" data-id="{{$b->_id}}">Edit</button>
                                        <button class="btn btn-warning btn-sm printBtn" data-id="{{$b->_id}}">Receipt</button>
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
{{-- Modals for create bouquet --}}
<div class="modal fade" id="createBouquetModal" tabindex="-1" aria-labelledby="exampleModalLgLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLgLabel">Add Product</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="inventoryTableContainer">
                    <!-- Inventory table will be loaded here dynamically -->
                </div>
                <div class="card mt-3">
                    <div class="card-header">Customer Details (Optional)</div>
                    <div class="card-body">
                        <input type="text" class="form-control mb-2" name="customer_name" placeholder="Customer Name" autocomplete="off">
                        <input type="email" class="form-control mb-2" name="customer_email" placeholder="Customer Email" autocomplete="off">
                        <input type="tel" maxlength="10" class="form-control mb-2" name="customer_phone" placeholder="Customer Phone" autocomplete="off">
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">Delivery Details (Optional)</div>
                    <div class="card-body">
                        <input type="date" class="form-control mb-2" name="delivery_date">
                        <textarea class="form-control" name="delivery_address" placeholder="Delivery Address" autocomplete="off"></textarea>
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label for="bouquet_image" class="form-label">Bouquet Image</label>
                    <input type="file" class="form-control" id="bouquet_image" name="bouquet_image" accept="image/*" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="createBouquetBtn">Create Bouquet</button>
            </div>
        </div>
    </div>
</div>
{{-- Modal to Show Bouquet Details --}}
<div class="modal fade" id="bouquetInfoModal" tabindex="-1" aria-labelledby="bouquetInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bouquetInfoModalLabel">Bouquet Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="bouquetImage" src="" alt="Bouquet Image" class="img-fluid" style="max-height: 300px; object-fit: contain;">
                </div>
                <h6>Items in Bouquet</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="bouquetItemsTable">
                        <!-- Items will be dynamically added here -->
                    </tbody>
                </table>
                <h6 class="mt-3">Details</h6>
                <p><strong>Total Price:</strong> <span id="bouquetTotalPrice"></span></p>
                <p><strong>Customer Name:</strong> <span id="bouquetCustomerName"></span></p>
                <p><strong>Customer Email:</strong> <span id="bouquetCustomerEmail"></span></p>
                <p><strong>Customer Phone:</strong> <span id="bouquetCustomerPhone"></span></p>
                <p><strong>Delivery Date:</strong> <span id="bouquetDeliveryDate"></span></p>
                <p><strong>Delivery Address:</strong> <span id="bouquetDeliveryAddress"></span></p>
            </div>
        </div>
    </div>
</div>
{{-- Modal to Edit Bouquet --}}
<div class="modal fade" id="editBouquetModal" data-id="{{$b->_id}}" tabindex="-1" aria-labelledby="editBouquetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="editBouquetModalContent">
            <!-- Modal content will be loaded dynamically -->
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

    $(document).on('click', '.create-bouquet', function() {
        $("#createBouquetModal").modal('show');
        $.ajax({
            url: "{{ route('bouquet.fetch.inventory') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#inventoryTableContainer').html(response);
            },
            error: function(xhr) {
                toastr.error('Failed to load inventory.');
            }
        });
    });

    $(document).on('click', '#createBouquetBtn', function() {
        const deliveryDate = $('input[name="delivery_date"]').val();
        const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format

        // Check if the delivery date is valid
        if (deliveryDate && deliveryDate < today) {
            toastr.error('Delivery date must be today or a future date.');
            return; // Stop form submission
        }
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('bouquet_image', $('#bouquet_image')[0].files[0]);
        formData.append('customer_name', $('input[name="customer_name"]').val());
        formData.append('customer_email', $('input[name="customer_email"]').val());
        formData.append('customer_phone', $('input[name="customer_phone"]').val());
        formData.append('delivery_date', $('input[name="delivery_date"]').val());
        formData.append('delivery_address', $('textarea[name="delivery_address"]').val());

        // Collect items as an array
        $('#inventoryTableContainer').find('tr').each(function(index) {
            let quantity = $(this).find('.add-quantity').val();
            let id = $(this).find('input[type="hidden"]').val();
            if (quantity > 0) {
                formData.append(`items[${index}][id]`, id); // Append item ID
                formData.append(`items[${index}][quantity]`, quantity); // Append item quantity
            }
        });

        $.ajax({
            url: "{{ route('bouquet.create') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#createBouquetModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to create bouquet.');
            }
        });
    });

    $(document).on('click', '.infoBtn', function() {
        const bouquetId = $(this).data('id'); // Get the bouquet ID from the button's data attribute

        // Clear previous modal content
        $('#bouquetImage').attr('src', '');
        $('#bouquetItemsTable').html('');
        $('#bouquetTotalPrice').text('');
        $('#bouquetCustomerName').text('');
        $('#bouquetCustomerEmail').text('');
        $('#bouquetCustomerPhone').text('');
        $('#bouquetDeliveryDate').text('');
        $('#bouquetDeliveryAddress').text('');

        $.ajax({
            url: "{{ route('bouquet.details') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                bouquet_id: bouquetId,
            },
            success: function(response) {
                if (response.success) {
                    const bouquet = response.bouquet;

                    // Set the bouquet image
                    $('#bouquetImage').attr('src', bouquet.bouquet_image);

                    // Populate the items table
                    let itemsHtml = '';
                    bouquet.items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td>${item.item_name}</td>
                                <td>${item.quantity}</td>
                            </tr>
                        `;
                    });
                    $('#bouquetItemsTable').html(itemsHtml);

                    // Set other details
                    $('#bouquetTotalPrice').text(bouquet.total_price);
                    $('#bouquetCustomerName').text(bouquet.customer_name || 'N/A');
                    $('#bouquetCustomerEmail').text(bouquet.customer_email || 'N/A');
                    $('#bouquetCustomerPhone').text(bouquet.customer_phone || 'N/A');
                    $('#bouquetDeliveryDate').text(bouquet.delivery_date || 'N/A');
                    $('#bouquetDeliveryAddress').text(bouquet.delivery_address || 'N/A');

                    // Show the modal
                    $('#bouquetInfoModal').modal('show');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to fetch bouquet details.');
            }
        });
    });

    $(document).on('click', '.editBtn', function() {
        const bouquetId = $(this).data('id'); // Get the bouquet ID from the button's data attribute

        $.ajax({
            url: "{{ route('bouquet.edit.modal') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                bouquet_id: bouquetId,
            },
            success: function(response) {
                $('#editBouquetModalContent').html(response); // Load the modal content dynamically
                $('#editBouquetModal').modal('show'); // Show the modal
            },
            error: function(xhr) {
                toastr.error('Failed to load bouquet details.');
            }
        });
    });

    $(document).on('click', '#updateBouquetBtn', function() {
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('bouquet_id', $('#editBouquetModal').data('id'));
        formData.append('edit_customer_name', $('input[name="edit_customer_name"]').val());
        formData.append('edit_customer_email', $('input[name="edit_customer_email"]').val());
        formData.append('edit_customer_phone', $('input[name="edit_customer_phone"]').val());
        formData.append('edit_delivery_date', $('input[name="edit_delivery_date"]').val());
        formData.append('edit_delivery_address', $('textarea[name="edit_delivery_address"]').val());

        // Collect items as an array
        $('.edit-quantity').each(function(index) {
            const id = $(this).data('id');
            const quantity = $(this).val();
            if (quantity > 0) {
                formData.append(`items[${index}][id]`, id); // Append item ID
                formData.append(`items[${index}][quantity]`, quantity); // Append item quantity
            }
        });

        $.ajax({
            url: "{{ route('bouquet.update') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#editBouquetModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to update bouquet.');
            }
        });
    });
});
</script>

@endsection