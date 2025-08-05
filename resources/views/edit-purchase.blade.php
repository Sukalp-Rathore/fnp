<form action="{{ route('purchase.update') }}" method="POST" id="updatePurchaseForm">
    @csrf
    <input type="hidden" value="{{ $purchase->_id }}" name="purchaseId" id="purchaseId">
    <div class="row gy-3">
        <div class="col-xl-12 form-group">
            <label for="purchase_person" class="form-label text-default">Purchase Person Name</label>
            <select class="sel form-control" name="purchase_person" id="purchase_person">
                <option value="" disabled>Select Purchase Person Name</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->name }}"
                        {{ $vendor->name == $purchase->purchase_person ? 'selected' : '' }}>{{ $vendor->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-xl-12 form-group">
            <label for="amount" class="form-label text-default">Amount (Rs)</label>
            <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Purchase Amount"
                value="{{ $purchase->amount }}" autocomplete="off" required>
        </div>
        <div class="col-xl-12 form-group">
            <label for="payment_mode" class="form-label text-default">Payment Mode</label>
            <select class="sell form-control" name="payment_mode" id="payment_mode">
                <option value="" disabled selected>Select Payment Method</option>
                <option value="online" {{ $purchase->payment_mode == 'online' ? 'selected' : '' }}>Online</option>
                <option value="cash" {{ $purchase->payment_mode == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="none" {{ $purchase->payment_mode == 'none' ? 'selected' : '' }}>None</option>
            </select>
        </div>
        <div class="col-xl-12 form-group">
            <label for="payment_status" class="form-label text-default">Payment Status</label>
            <select class="sell form-control" name="payment_status" id="payment_status">
                <option value="" disabled>Select Payment Status</option>
                <option value="pending" {{ $purchase->payment_status == 'pending' ? 'selected' : '' }}>Pending
                </option>
                <option value="part-payment" {{ $purchase->payment_status == 'part-payment' ? 'selected' : '' }}>
                    Part Payment</option>
                <option value="paid" {{ $purchase->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>
        <div class="col-xl-12 form-group">
            <label for="date">Entry Date</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ $purchase->date }}"
                required>
        </div>
        <div class="col-xl-12 form-group">
            <label for="paid_amount" class="form-label text-default">Amount Paid (Rs)</label>
            <input type="number" class="form-control" id="paid_amount" name="paid_amount"
                placeholder="Enter Amount Paid" value="{{ $purchase->paid_amount }}" autocomplete="off" required>
        </div>
    </div>
    <div class="modal-footer">
        <center>
            <button type="submit" class="btn btn-primary">Update Entry</button>
        </center>
    </div>
</form>

<script>
    $('#updatePurchaseForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editPurchaseModal').modal('hide');
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
