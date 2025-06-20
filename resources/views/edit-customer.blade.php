<form id="addCustomerForm" method="POST" action="{{ route('customer.update') }}">
    {{-- CSRF Token --}}
    @csrf
    <input type="hidden" name="customerId" id="customerId" value="{{ $customer->_id }}">
    <div class="mb-3">
        <label for="customer_name" class="form-label">Customer Name</label>
        <input type="text" class="form-control" value="{{ $customer->customer_name }}" id="customer_name"
            name="customer_name" placeholder="Enter full customer name" required autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="customer_email" class="form-label">Customer Email</label>
        <input type="email" class="form-control" value="{{ $customer->customer_email }}" id="customer_email"
            name="customer_email" placeholder="Enter customer email" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="customer_phone" class="form-label">Mobile No.</label>
        <input type="tel" maxlength="10" class="form-control" value="{{ $customer->customer_phone }}"
            id="customer_phone" placeholder="Enter 10 digit customer phone number" name="customer_phone"
            autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="customer_address" class="form-label">Customer address</label>
        <input type="text" class="form-control" id="customer_address" value="{{ $customer->customer_address }}"
            placeholder="Enter customer address" name="customer_address" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="customer_type" class="form-label">Customer Type</label>
        <select class="form-select" id="customer_type" name="customer_type">
            <option value="Primary"
                {{ isset($customer) && strtolower($customer->customer_type) == 'primary' ? 'selected' : '' }}>Primary
            </option>
            <option value="Secondary"
                {{ isset($customer) && strtolower($customer->customer_type) == 'secondary' ? 'selected' : '' }}>
                Secondary</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="event_name" class="form-label">Choose event</label>
        <select class="form-select" id="event_name" name="event_name">
            @foreach ($events as $event)
                <option value="{{ $event }}"
                    {{ isset($customer) && strtolower($customer->event_name) == strtolower($event) ? 'selected' : '' }}>
                    {{ $event }}</option>
            @endforeach
            <option value=""
                {{ isset($customer) && strtolower($customer->event_name) == '' ? 'selected' : '' }}>None</option>
        </select>
    </div>
    <div class="mb-3">
        <button class="btn btn-secondary" type="submit">Edit Customer</button>
    </div>
</form>


<script>
    $('#editCustomerForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editVendorModal').modal('hide');
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
