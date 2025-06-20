<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">

<table class="table table-bordered" id="datatable-basic">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Product Name</th>
            <th>Type</th>
            <th>Selling Price</th>
            <th>Quantity</th>
            <th>Image</th>
            <th>Add to Bouquet</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($inventory as $item)
            @php
                // Check if quantity exists and is greater than 0
                $quantity = $item->quantity ?? 0;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->type }}</td>
                <td>{{ $item->selling_price }}</td>
                <td>{{ $item->quantity }}</td>
                <td>
                    <button class="btn btn-sm btn-info viewImageBtn" data-image="{{ $item->product_image }}">View</button>
                </td>
                <td>
                    <input type="number" class="form-control add-quantity" name="items[{{ $loop->index }}][quantity]"
                        min="0" max="{{ $quantity }}" placeholder="0" {{ $quantity <= 0 ? 'disabled' : '' }}>
                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->_id }}">
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Modal to View Product Image --}}
<div class="modal fade" id="viewImageModal" tabindex="-1" aria-labelledby="viewImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewImageModalLabel">Product Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="productImagePreview" src="" alt="Product Image" class="img-fluid"
                    style="max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
{{-- End of Modal to View Product Image --}}

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
    document.addEventListener("DOMContentLoaded", (event) => {
        $('#datatable-basic').DataTable({
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
            },
            "pageLength": 10,
            // scrollX: true
        });

        $(document).on('click', '.viewImageBtn', function() {
            const imageUrl = $(this).data(
                'image'); // Get the image URL from the button's data attribute
            $('#productImagePreview').attr('src', imageUrl); // Set the image URL in the modal
            $('#viewImageModal').modal('show'); // Show the modal
        });
    });
</script>
