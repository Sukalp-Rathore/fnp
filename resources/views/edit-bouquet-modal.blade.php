<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">


<div class="modal-header">
    <h5 class="modal-title" id="editBouquetModalLabel">Edit Bouquet</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div id="editInventoryTableContainer">
        <table class="table table-bordered" id="datatable-basic">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Color</th>
                    <th>Type</th>
                    <th>Selling Price</th>
                    <th>Current Quanity In Bouquet</th>
                    <th>Available Quantity</th>
                    <th>Quantity in Bouquet</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventory as $item)
                    @php
                        $quantity = $item->quantity ?? 0;

                        $bouquetItem = collect($bouquet->items)->first(function ($bItem) use ($item) {
                            return $bItem['item_name'] === $item->product_name && $bItem['color'] === $item->color;
                        });

                        $quantityInBouquet = $bouquetItem ? $bouquetItem['quantity'] : 0;
                    @endphp

                    <tr class="{{ $quantityInBouquet > 0 ? 'table-success' : '' }}">
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->color }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->selling_price }}</td>
                        <td data-order="{{ $quantityInBouquet }}">{{ $quantityInBouquet }}</td>
                        <td>{{ $quantity }}</td>
                        <td>
                            <input type="number" class="form-control edit-quantity"
                                name="items[{{ $loop->index }}][quantity]" value="{{ $quantityInBouquet }}"
                                min="0" max="{{ $item->quantity + $quantityInBouquet }}"
                                data-id="{{ $item->_id }}"
                                {{ $quantity + $quantityInBouquet <= 0 ? 'disabled' : '' }}>
                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->_id }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card mt-3">
        <div class="card-header">Making Charge</div>
        <div class="card-body">
            <input type="text" class="form-control mb-2" name="making_charge_edit"
                value="{{ $bouquet->making_charge }}" placeholder="Making Charge" autocomplete="off">
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">Created By</div>
        <div class="card-body">
            <input type="text" class="form-control mb-2" name="created_by_edit"
                placeholder="Person Name (Who created this bouquet)" value="{{ $bouquet->created_by }}"
                autocomplete="off">
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">Customer Details</div>
        <div class="card-body">
            <input type="text" class="form-control mb-2" name="edit_customer_name"
                value="{{ $bouquet->customer_name }}" placeholder="Customer Name">
            <input type="email" class="form-control mb-2" name="edit_customer_email"
                value="{{ $bouquet->customer_email }}" placeholder="Customer Email">
            <input type="tel" maxlength="10" class="form-control mb-2" name="edit_customer_phone"
                value="{{ $bouquet->customer_phone }}" placeholder="Customer Phone">
            <input type="hidden" id="bouquet_id" name="bouquet_id" value="{{ $bouquet->_id }}"
                class="form-control mb-2" placeholder="Bouquet ID">
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">Delivery Details</div>
        <div class="card-body">
            <input type="date" class="form-control mb-2" name="edit_delivery_date"
                value="{{ $bouquet->delivery_date }}">
            <textarea class="form-control" name="edit_delivery_address" placeholder="Delivery Address">{{ $bouquet->delivery_address }}</textarea>
        </div>
    </div>
    <div class="mb-3">
        <label for="bouquet_imagett" class="form-label">Bouquet Image</label>
        <div class="mb-2">
            <img id="imagePreview" src="{{ $bouquet->bouquet_image }}" alt="bouquet Image"
                style="width: 150px; height: 150px; object-fit: cover;">
        </div>
        <input type="file" class="form-control" id="bouquet_imagett" name="edit_bouquet_image" accept="image/*">
    </div>
    {{-- <div class="mb-3 mt-3">
        <label for="edit_bouquet_image" class="form-label">Bouquet Image</label>
        <input type="file" class="form-control" id="edit_bouquet_image" name="edit_bouquet_image" accept="image/*">
    </div> --}}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="updateBouquetBtn">Update Bouquet</button>
</div>

<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="assets/js/datatables.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let bouquetItems = {!! json_encode(
            collect($bouquet->items)->map(function ($item) {
                return strtolower(trim($item['item_name'] . '|' . $item['color']));
            }),
        ) !!};

        if ($.fn.DataTable.isDataTable('#datatable-basic')) {
            $('#datatable-basic').DataTable().destroy();
        }

        $('#datatable-basic').DataTable({
            pageLength: 100,
            ordering: false,
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Image preview
        $('#bouquet_imagett').on('change', function() {
            const file = this.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        $(document).on('click', '#updateBouquetBtn', function() {
            let formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('bouquet_id', $('input[name="bouquet_id"]').val());
            formData.append('making_charge_edit', $('input[name="making_charge_edit"]').val());
            formData.append('created_by_edit', $('input[name="created_by_edit"]').val());
            formData.append('edit_customer_name', $('input[name="edit_customer_name"]').val());
            formData.append('edit_customer_email', $('input[name="edit_customer_email"]').val());
            formData.append('edit_customer_phone', $('input[name="edit_customer_phone"]').val());
            formData.append('edit_delivery_date', $('input[name="edit_delivery_date"]').val());
            formData.append('edit_delivery_address', $('textarea[name="edit_delivery_address"]').val());

            // Collect items as an array
            var table = $('#datatable-basic').DataTable();
            table.$('.edit-quantity').each(function(index) {
                const id = $(this).data('id');
                const quantity = $(this).val();
                if (quantity > 0) {
                    formData.append(`items[${index}][id]`, id); // Append item ID
                    formData.append(`items[${index}][quantity]`,
                    quantity); // Append item quantity
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
