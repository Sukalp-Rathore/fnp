<table class="table table-bordered">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Product Name</th>
            <th>Current Quantity</th>
            <th>Update Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->quantity ?? 0 }}</td>
                <td>
                    <input type="number" class="form-control update-quantity" name="stock[{{ $loop->index }}][quantity]" value="0" min="0">
                    <input type="hidden" name="stock[{{ $loop->index }}][id]" value="{{ $product->_id }}">
                </td>
            </tr>
        @endforeach
    </tbody>
</table>