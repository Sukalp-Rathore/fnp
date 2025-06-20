@extends('layout')
@section('content')
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
                        <a class="btn btn-primary btn-sm openForm">Enter Today's Sales</a>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $v)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $v->cash_sale }}</td>
                                        <td>{{ $v->online_sale }}</td>
                                        <td>{{ $v->credit_sale }}</td>
                                        <td>{{ $v->created_at ?? 'N/A' }}</td>
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
        });
    </script>
@endsection
