<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
<table class="table table-bordered" id="file-exportss">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile No.</th>
            <th>Event</th>
            <th>Event Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($secondaryCustomers as $c)
            <tr>
                <td>{{ $c['customer_name'] }}</td>
                <td>{{ $c['customer_email'] }}</td>
                <td>{{ $c['customer_phone'] }}</td>
                <td>{{ $c['event_name'] }}</td>
                <td>{{ getutc($c['event_date'], 'd.m.Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

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
        $('#file-exportss').DataTable({
            pageLength: 10,
            ordering: false,
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
            }
        });
    });
</script>
