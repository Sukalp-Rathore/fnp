<form id="editVendorForm" method="POST" action="{{ route('vendor.update') }}">
    {{-- CSRF Token --}}
    @csrf
    <input type="hidden" value="{{ $vendor->_id }}" name="vendorId" id="vendorId">
    <div class="mb-3">
        <label for="first_name" class="form-label">Vendor Name</label>
        <input type="text" class="form-control" id="first_name" value="{{ $vendor->first_name ?? '' }}"
            name="first_name" placeholder="Enter full vendor name" required autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ $vendor->email ?? '' }}"
            placeholder="Enter vendor email" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="mobile" class="form-label">Mobile No.</label>
        <input type="tel" maxlength="10" class="form-control" id="mobile" value="{{ $vendor->mobile ?? '' }}"
            placeholder="Enter 10 digit mobile number" name="mobile" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="alternate_mobile" class="form-label">Alternate Mobile No.</label>
        <input type="tel" maxlength="10" class="form-control" id="alternate_mobile"
            value="{{ $vendor->alternate_mobile ?? 'N/A' }}" placeholder="Enter 10 digit mobile number"
            name="alternate_mobile" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="city" class="form-label">City</label>
        <input type="text" class="form-control" id="city" placeholder="Enter city name"
            value="{{ $vendor->city }}" name="city" autocomplete="off">
    </div>
    <div class="mb-3">
        <label for="gender" class="form-label">Gender</label>
        <select class="form-select" id="gender" name="gender">
            <option value="Male" {{ isset($vendor) && strtolower($vendor->gender) == 'male' ? 'selected' : '' }}>
                Male</option>
            <option value="Female" {{ isset($vendor) && strtolower($vendor->gender) == 'female' ? 'selected' : '' }}>
                Female</option>
        </select>
    </div>
    <div class="mb-3">
        <button class="btn btn-secondary" type="submit">Update Vendor</button>
    </div>
</form>

<script>
    $('#editVendorForm').on('submit', function(e) {
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
