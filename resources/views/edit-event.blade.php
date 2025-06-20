<form id="editEventForm" method="POST" action="{{ route('event.update') }}">
    {{-- CSRF Token --}}
    @csrf
    <input type="hidden" value="{{ $event->_id }}" name="event_id" id="event_id">
    <div class="mb-3">
        <label for="events" class="form-label">Event Name</label>
        <input type="text" class="form-control" id="events" value="{{ $event->events ?? '' }}" name="events"
            placeholder="Enter full vendor name" required autocomplete="off">
    </div>
    <div class="form-group mb-3">
        <div class="input-group">
            <div class="input-group-text text-muted"> <i class="ri-calendar-line"></i> </div>
            <input type="date" class="form-control" name="event_date" id="date" placeholder="Choose date"
                value="{{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') }}" required autocomplete="off">
        </div>
    </div>
    <div class="mb-3">
        <button class="btn btn-secondary" type="submit">Update Vendor</button>
    </div>
</form>

<script>
    $('#editEventForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editEventModal').modal('hide');
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
