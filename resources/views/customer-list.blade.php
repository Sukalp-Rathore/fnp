@extends('layout')
@section('content')
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <h1 class="page-title fw-medium fs-18 mb-0">Customers List</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">Get secondary customers by primary</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="file-exports" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile No.</th>
                                    <th>Event</th>
                                    <th>Event Date</th>
                                    <th>Created At</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $c)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $c->customer_name }}</td>
                                        <td>{{ $c->customer_email }}</td>
                                        <td>{{ $c->customer_phone }}</td>
                                        <td>{{ $c->event_name }}</td>
                                        <td>{{ $c->event_date }}</td>
                                        <td>{{ $c->created_at }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary showBtn"
                                                data-id="{{ $c->_id }}">Show Secondary</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals for add vendor  --}}
    <div class="modal fade" id="showSecondaryCustomers" tabindex="-1" aria-labelledby="exampleModalLgLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLgLabel">Add Customer</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="responsed">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- modal end --}}
@endsection


@section('js')
    <script>
        $(document).ready(function() {
            var table = $('#file-exports').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'collection',
                    text: '<i class="fa fa-download"></i> Export',
                    buttons: [{
                            extend: 'copy',
                            text: '<i class="fa fa-copy"></i> Copy'
                        },
                        {
                            extend: 'csv',
                            text: '<i class="fa fa-file-csv"></i> CSV'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fa fa-file-excel"></i> Excel'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fa fa-file-pdf"></i> PDF'
                        }
                    ]
                }],
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                }
            });

            $(document).on('click', '.showBtn', function() {
                let customerId = $(this).attr('data-id');
                $("#showSecondaryCustomers").modal('show');
                $.ajax({
                    url: "{{ route('customers.secondary') }}",
                    type: 'POST',
                    data: {
                        customerId: customerId,
                    },
                    success: function(response) {
                        if (response.success == false) {
                            toastr.error(response.message);
                            $("#showSecondaryCustomers").modal('hide');
                        } else {
                            $("#responsed").html(response);
                        };
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
