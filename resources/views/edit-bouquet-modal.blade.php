<div class="modal-header">
    <h5 class="modal-title" id="editBouquetModalLabel">Edit Bouquet</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div id="editInventoryTableContainer">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Type</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Available Quantity</th>
                    <th>Quantity in Bouquet</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventory as $item)
                    @php
                        $bouquetItem = collect($bouquet->items)->firstWhere('item_name', $item->product_name);
                        $quantityInBouquet = $bouquetItem ? $bouquetItem['quantity'] : 0;
                    @endphp
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->cost_price }}</td>
                        <td>{{ $item->selling_price }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>
                            <input type="number" class="form-control edit-quantity" name="items[{{ $loop->index }}][quantity]" value="{{ $quantityInBouquet }}" min="0" max="{{ $item->quantity + $quantityInBouquet }}" data-id="{{ $item->_id }}">
                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->_id }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card mt-3">
        <div class="card-header">Customer Details</div>
        <div class="card-body">
            <input type="text" class="form-control mb-2" name="edit_customer_name" value="{{ $bouquet->customer_name }}" placeholder="Customer Name">
            <input type="email" class="form-control mb-2" name="edit_customer_email" value="{{ $bouquet->customer_email }}" placeholder="Customer Email">
            <input type="tel" maxlength="10" class="form-control mb-2" name="edit_customer_phone" value="{{ $bouquet->customer_phone }}" placeholder="Customer Phone">
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header">Delivery Details</div>
        <div class="card-body">
            <input type="date" class="form-control mb-2" name="edit_delivery_date" value="{{ $bouquet->delivery_date }}">
            <textarea class="form-control" name="edit_delivery_address" placeholder="Delivery Address">{{ $bouquet->delivery_address }}</textarea>
        </div>
    </div>
    <div class="mb-3 mt-3">
        <label for="edit_bouquet_image" class="form-label">Bouquet Image</label>
        <input type="file" class="form-control" id="edit_bouquet_image" name="edit_bouquet_image" accept="image/*">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="updateBouquetBtn">Update Bouquet</button>
</div>