@extends('layout')
@section('content')
    <!-- jQuery Confirm CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/css/jquery-confirm.min.css">
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <h1 class="page-title fw-medium fs-18 mb-0">Sales</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">All Sales</div>
                    <div class="card-options">
                        <a class="btn btn-primary btn-sm openForm">Enter Sales</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="file-exportt" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Cash Sales(Rs)</th>
                                    <th>Online Sales(Rs)</th>
                                    <th>Credit Sales(Rs)</th>
                                    <th>Date</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $v)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $v->cash_sale }}</td>
                                        <td>{{ $v->online_sale }}</td>
                                        <td>{{ $v->credit_sale }}</td>
                                        <td>{{ getutc($v->date, 'd.m.Y') ?? 'N/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-danger deleteBtn"
                                                data-id="{{ $v->_id }}">Delete</button>
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

    {{-- Modal to enter sale  --}}
    <div class="modal fade" id="enterSalesModal" tabindex="-1" aria-labelledby="exampleModalScrollable2"
        data-bs-keyboard="false" aria-hidden="true">
        <!-- Scrollable modal -->
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="staticBackdropLabel2">Enter Todays Sales
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('sales.enter') }}" method="POST" id="salesForm">
                    <div class="modal-body">
                        @csrf
                        <div class="row gy-3">
                            <div class="col-xl-12 form-group">
                                <label for="cashsales" class="form-label text-default">Cash Sales(Rs)</label>
                                <input type="number" class="form-control" id="cashsales" name="cashsales"
                                    placeholder="Cash Sales" autocomplete="off" required>
                            </div>
                            <div class="col-xl-12 form-group">
                                <label for="creditsales" class="form-label text-default">Credit Sales(Rs)</label>
                                <input type="number" class="form-control" id="creditsales" name="creditsales"
                                    placeholder="Credit Sales" autocomplete="off" required>
                            </div>
                            <div class="col-xl-12 form-group">
                                <label for="onlinesales" class="form-label text-default">Online Sales(Rs)</label>
                                <input type="number" class="form-control" id="onlinesales" name="onlinesales"
                                    placeholder="Online Sales" autocomplete="off" required>
                            </div>
                            <div class="col-xl-12 form-group">
                                <label for="date" class="form-label text-default">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <center>
                            <button type="submit" class="btn btn-primary">Create Entry</button>
                        </center>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end modal  --}}
@endsection
@section('js')
    <!-- jQuery Confirm JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-confirm@3.3.4/js/jquery-confirm.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#file-exportt').DataTable({
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

            $(document).on('click', '.openForm', function() {
                $("#enterSalesModal").modal('show');
            });

            $('#salesForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    },
                    error: function(response) {
                        if (response.responseJSON && response.responseJSON.error) {
                            toastr.error(response.responseJSON.error);
                        } else {
                            toastr.error("An unexpected error occurred.");
                        }
                    }
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                let saleId = $(this).attr('data-id');

                $.confirm({
                    title: 'Confirm Deletion',
                    content: 'Are you sure you want to delete this sale?',
                    buttons: {
                        confirm: {
                            text: 'Yes, Delete',
                            btnClass: 'btn-red',
                            action: function() {
                                $.ajax({
                                    url: "{{ route('sale.delete') }}",
                                    type: 'POST',
                                    data: {
                                        saleId: saleId,
                                        _token: '{{ csrf_token() }}' // add CSRF token manually if needed
                                    },
                                    success: function(response) {
                                        if (response.success == false) {
                                            $.alert({
                                                title: 'Error',
                                                content: response.message,
                                                type: 'red'
                                            });
                                        } else {
                                            toastr.success(response.message);
                                            setTimeout(function() {
                                                window.location.reload();
                                            }, 1000);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error(xhr.responseText);
                                        $.alert({
                                            title: 'Ajax Error',
                                            content: 'Something went wrong while deleting.',
                                            type: 'red'
                                        });
                                    }
                                });
                            }
                        },
                        cancel: function() {
                            // Do nothing on cancel
                        }
                    }
                });
            });

        });
    </script>
@endsection
