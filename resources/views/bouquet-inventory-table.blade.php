<table class="table table-bordered">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Product Name</th>
            <th>Type</th>
            <th>Cost Price</th>
            <th>Selling Price</th>
            <th>Quantity</th>
            <th>Image</th>
            <th>Add to Bouquet</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($inventory as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->type }}</td>
                <td>{{ $item->cost_price }}</td>
                <td>{{ $item->selling_price }}</td>
                <td>{{ $item->quantity }}</td>
                <td>
                    <button class="btn btn-sm btn-info viewImageBtn" data-image="{{ $item->product_image }}">View</button>
                </td>
                <td>
                    <input type="number" class="form-control add-quantity" name="items[{{ $loop->index }}][quantity]" min="0" max="{{ $item->quantity }}" placeholder="0">
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
                <img id="productImagePreview" src="" alt="Product Image" class="img-fluid" style="max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
{{-- End of Modal to View Product Image --}}

<script>
    $(document).on('click', '.viewImageBtn', function() {
        const imageUrl = $(this).data('image'); // Get the image URL from the button's data attribute
        $('#productImagePreview').attr('src', imageUrl); // Set the image URL in the modal
        $('#viewImageModal').modal('show'); // Show the modal
    });
</script>