<form id="editProductForm" method="POST" action="{{ route('inventory.update') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->_id }}">

    <div class="mb-3">
        <label for="product_name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="product_name" name="product_name" value="{{ $product->product_name }}" required>
    </div>

    <div class="mb-3">
        <label for="type" class="form-label">Product Type</label>
        <select class="form-select" id="type" name="type" required>
            <option value="flower" {{ $product->type == 'flower' ? 'selected' : '' }}>Flower</option>
            <option value="fillable" {{ $product->type == 'fillable' ? 'selected' : '' }}>Fillables</option>
            <option value="chocolate" {{ $product->type == 'chocolate' ? 'selected' : '' }}>Chocolates</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="cost_price" class="form-label">Cost Price</label>
        <input type="number" class="form-control" id="cost_price" name="cost_price" value="{{ $product->cost_price }}" required>
    </div>

    <div class="mb-3">
        <label for="selling_price" class="form-label">Selling Price</label>
        <input type="number" class="form-control" id="selling_price" name="selling_price" value="{{ $product->selling_price }}" required>
    </div>

    <div class="mb-3">
        <label for="product_image" class="form-label">Product Image</label>
        <div class="mb-2">
            <img id="imagePreview" src="{{ $product->product_image }}" alt="Product Image" style="width: 150px; height: 150px; object-fit: cover;">
        </div>
        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">Update Product</button>
</form>

<script>
    document.getElementById('product_image').addEventListener('change', function(event) {
        alert('Image selected');
        const file = event.target.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function(e) {
                // Set new image preview
                document.getElementById('imagePreview').setAttribute('src', e.target.result);
            };

            reader.readAsDataURL(file);
        }
    });
</script>
<script>
    $('#editProductForm').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            $('#editInventoryModal').modal('hide');
            toastr.success(response.message);
            setTimeout(() => {
                window.location.reload();
            }, 1000);
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
</script>
