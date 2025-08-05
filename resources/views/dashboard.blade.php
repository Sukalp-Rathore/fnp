@extends('layout')

@section('content')
    <style>
        input[type="checkbox"] {
            border: 1px solid black !important;
        }
    </style>
    <!-- FlatPickr CSS -->
    <link rel="stylesheet" href="assets/libs/flatpickr/flatpickr.min.css">
    <!-- Prism CSS -->
    <link rel="stylesheet" href="assets/libs/prismjs/themes/prism-coy.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">


    <!-- Start::page-header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <h1 class="page-title fw-medium fs-18 mb-0">FNP Dashboard</h1>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="btn-list">
                <form method="GET" action="{{ route('dashboard') }}">
                    <input type="hidden" name="filter_by_this_month" value="true">
                    <button type="submit" class="btn btn-white btn-wave">
                        <i class="ri-filter-3-line align-middle me-1 lh-1"></i> This Month
                    </button>
                </form>

            </div>
        </div>
    </div>
    <!-- End::page-header -->
    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                <div class="col-xxl-3 col-xl-6">
                    <div class="card custom-card overflow-hidden main-content-card">
                        <div class="card-body">
                            <div
                                class="d-flex align-items-start justify-content-between mb-2 gap-1 flex-xxl-nowrap flex-wrap">
                                <div>
                                    <span class="text-muted d-block mb-1 text-nowrap">Total Sales</span>
                                    <h4 class="fw-medium mb-0">{{ $totalSales }}</h4>
                                </div>
                                <div class="lh-1">
                                    <span class="avatar avatar-md avatar-rounded bg-primary">
                                        <i class="bi bi-wallet fs-5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-6">
                    <div class="card custom-card overflow-hidden main-content-card">
                        <div class="card-body">
                            <div
                                class="d-flex align-items-start justify-content-between mb-2 gap-1 flex-xxl-nowrap flex-wrap">
                                <div>
                                    <span class="text-muted d-block mb-1 text-nowrap">Total Purchase</span>
                                    <h4 class="fw-medium mb-0">{{ $totalPurchase }}</h4>
                                </div>
                                <div class="lh-1">
                                    <span class="avatar avatar-md avatar-rounded bg-primary1">
                                        <i class="bi bi-currency-rupee fs-5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-6">
                    <div class="card custom-card overflow-hidden main-content-card">
                        <div class="card-body">
                            <div
                                class="d-flex align-items-start justify-content-between mb-2 gap-1 flex-xxl-nowrap flex-wrap">
                                <div>
                                    <span class="text-muted d-block mb-1 text-nowrap">Total Bouquets</span>
                                    <h4 class="fw-medium mb-0">{{ $totalBouquets }}</h4>
                                </div>
                                <div class="lh-1">
                                    <span class="avatar avatar-md avatar-rounded bg-primary2">
                                        <i class="bi bi-flower1 fs-5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-6">
                    <div class="card custom-card overflow-hidden main-content-card">
                        <div class="card-body">
                            <div
                                class="d-flex align-items-start justify-content-between mb-2 gap-1 flex-xxl-nowrap flex-wrap">
                                <div>
                                    <span class="text-muted d-block mb-1 text-nowrap">Total Pending Orders</span>
                                    <h4 class="fw-medium mb-0">{{ $totalPendingOrders }}</h4>
                                </div>
                                <div class="lh-1">
                                    <span class="avatar avatar-md avatar-rounded bg-primary3">
                                        <i class="bi bi-box-seam-fill fs-5"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-4 col-xl-6">
            <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Upcoming Events(This Month)
                    </div>
                </div>
                <div class="card-body pt-1">

                    @if (sizeof($futureEventsInCurrentMonth) == 0)
                        <div class="alert alert-primary" role="alert">
                            No Festival Events This Month!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Event Name</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($futureEventsInCurrentMonth as $f)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="fw-medium">{{ $f->events }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ getutc($f->event_date, 'd.m.Y') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-xl-6">
            <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Top 5 Vendors
                    </div>
                </div>
                <div class="card-body pt-1">
                    @if (sizeof($topVendors) == 0)
                        <div class="alert alert-primary2" role="alert">
                            No Orders With Vendors!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Vendor Name</th>
                                        <th>Total Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topVendors as $f)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="fw-medium">{{ $f['vendor'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $f['total_revenue'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                    <div class="card-title" style="font-size:0.85rem !important">
                        Customers who Orders Last Year For Upcoming Event
                    </div>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#mailTemplateModal" class="send-mail">Send Mail</a>
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm send-whatsapp" data-bs-toggle="modal"
                            data-bs-target="#whatsappTemplateModal">Whatsapp</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (sizeof($customers) == 0)
                        <div class="alert alert-primary" role="alert">
                            No Customers Found!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="datatable-basics" class="table text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <input class="form-check-input" type="checkbox" id="select-all"
                                                value="" aria-label="...">
                                        </th>
                                        <th>S.No</th>
                                        <th>Customer Name</th>
                                        <th>Customer Phone</th>
                                        <th>Customer Email</th>
                                        <th>Sender Name</th>
                                        <th>Customer Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $c)
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" id="checkboxNoLabel02"
                                                    value="{{ $c->_id }}" aria-label="..." checked>
                                            </td>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div>
                                                        <span class="d-block fw-medium">{{ $c->customer_name }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $c->customer_phone }}
                                            </td>
                                            <td>
                                                {{ $c->customer_email }}
                                            </td>
                                            <td>
                                                {{ $c->sender_name }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-transparent">{{ $c->customer_type }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                    <div class="card-title" style="font-size:0.85rem !important">
                        Customers With Upcoming Event(15days)
                    </div>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-secondary btn-sm send-mail" data-bs-toggle="modal"
                            data-bs-target="#mailTemplateModal">Send Mail</a>
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm send-whatsapp" data-bs-toggle="modal"
                            data-bs-target="#whatsappTemplateModal">Whatsapp</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (sizeof($customersUp) == 0)
                        <div class="alert alert-primary" role="alert">
                            No Customers Found!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="datatable-basict" class="table text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <input class="form-check-input" type="checkbox" id="select-all2"
                                                value="" aria-label="...">
                                        </th>
                                        <th>S.No</th>
                                        <th>Customer Name</th>
                                        <th>Customer Phone</th>
                                        <th>Customer Email</th>
                                        <th>Event Date</th>
                                        <th>Event Name</th>
                                        <th>Sender Name</th>
                                        <th>Customer Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customersUp as $c)
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" id="check2"
                                                    value="{{ $c->_id }}" aria-label="...">
                                            </td>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div>
                                                        <span class="d-block fw-medium">{{ $c->customer_name }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $c->customer_phone }}
                                            </td>
                                            <td>
                                                {{ $c->customer_email }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-primary">{{ getutc($c->event_date, 'd.m.Y') }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $c->event_name }}</span>
                                            </td>
                                            <td>
                                                {{ $c->sender_name }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-transparent">{{ $c->customer_type }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                    <div class="card-title" style="font-size:0.85rem !important">
                        Primary Customers With Secondary Customers Upcoming Event(3days)
                    </div>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-secondary btn-sm send-mail-primary"
                            data-bs-toggle="modal" data-bs-target="#mailPrimaryTemplateModal">Send Mail</a>
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm send-whatsapp-primary"
                            data-bs-toggle="modal" data-bs-target="#whatsappPrimaryTemplateModal">Whatsapp</a>
                    </div>
                </div>
                <div class="card-body">
                    @if (sizeof($primaryWithUpcomingSecondary) == 0)
                        <div class="alert alert-primary" role="alert">
                            No Such Customers Found!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="datatable-basica" class="table text-nowrap table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <input class="form-check-input" type="checkbox" id="select-all3"
                                                value="" aria-label="...">
                                        </th>
                                        <th>S.No</th>
                                        <th>Customer Name(P)</th>
                                        <th>Customer Phone(P)</th>
                                        <th>Customer Email(P)</th>
                                        <th>Secondary Customer Name</th>
                                        <th>Event Date(S)</th>
                                        <th>Event Name(S)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($primaryWithUpcomingSecondary as $c)
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" id="check3"
                                                    value="{{ $c['primary_id'] }}"
                                                    data-secondary-name="{{ $c['secondary_name'] }}" aria-label="...">
                                            </td>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div>
                                                        <span class="d-block fw-medium">{{ $c['primary_name'] }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $c['primary_phone'] }}
                                            </td>
                                            <td>
                                                {{ $c['primary_email'] }}
                                            </td>
                                            <td>
                                                {{ $c['secondary_name'] }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-primary">{{ getutc($c['event_date'], 'd.m.Y') }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $c['event_name'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mail Template Selection Modal -->
    <div class="modal fade" id="mailTemplateModal" tabindex="-1" aria-labelledby="mailTemplateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mailTemplateModalLabel">Select Mail Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="mailTemplateForm">
                        <div class="mb-3">
                            <label for="mailTemplate" class="form-label">Choose Template</label>
                            <select class="form-select" id="mailTemplate" name="mail_template" required>
                                <option value="" selected disabled>Choose A Occasion</option>
                                <option value="diwali">Diwali</option>
                                <option value="chistmas">Christmas</option>
                                <option value="rakhi">Rakhi</option>
                                <option value="newyear">New Year</option>
                                <option value="birthday">Birthday</option>
                                <option value="anniversary">Anniversary</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Emails</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mail Template Primary Secondary Selection Modal -->
    <div class="modal fade" id="mailPrimaryTemplateModal" tabindex="-1" aria-labelledby="mailTemplateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mailTemplateModalLabel">Select Mail Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="mailPrimaryTemplateForm">
                        <div class="mb-3">
                            <label for="mailTemplate" class="form-label">Choose Template</label>
                            <select class="form-select" id="mailTemplate" name="mail_template" required>
                                <option value="" selected disabled>Choose A Occasion</option>
                                <option value="diwali1">Diwali</option>
                                <option value="rakhi1">Rakhi</option>
                                <option value="newyear1">New Year</option>
                                <option value="birthdays1">Birthday</option>
                                <option value="anniversary1">Anniversary</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Emails</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Template Selection Modal -->
    <div class="modal fade" id="whatsappTemplateModal" tabindex="-1" aria-labelledby="whatsappTemplateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="whatsappTemplateModalLabel">Select WhatsApp Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="whatsappTemplateForm">
                        <div class="mb-3">
                            <label for="whatsappTemplate" class="form-label">Choose Template</label>
                            <select class="form-select" id="whatsappTemplate" name="whatsapp_template" required>
                                <option value="" selected disabled>Choose A Occasion</option>
                                <option value="diwali">Diwali</option>
                                <option value="christmas">Christmas</option>
                                <option value="rakhi">Rakhi</option>
                                <option value="newyear">New Year</option>
                                <option value="birthdays">Birthday</option>
                                <option value="anniversary">Anniversary</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Send WhatsApp Messages</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Template Primary Secondary Selection Modal -->
    <div class="modal fade" id="whatsappPrimaryTemplateModal" tabindex="-1"
        aria-labelledby="whatsappTemplateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="whatsappTemplateModalLabel">Select WhatsApp Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="whatsappPrimaryTemplateForm">
                        <div class="mb-3">
                            <label for="whatsappTemplate" class="form-label">Choose Template</label>
                            <select class="form-select" id="whatsappTemplate" name="whatsapp_template" required>
                                <option value="" selected disabled>Choose A Occasion</option>
                                <option value="diwali1">Diwali</option>
                                <option value="rakhi1">Rakhi</option>
                                <option value="newyear1">New Year</option>
                                <option value="birthdays1">Birthday</option>
                                <option value="anniversary1">Anniversary</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Send WhatsApp Messages</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Sales Dashboard -->
    <script src="assets/js/sales-dashboard.js"></script>
    <!-- Prism JS -->
    <script src="assets/libs/prismjs/prism.js"></script>
    <script src="assets/js/prism-custom.js"></script>

    <!-- Alerts JS -->
    <script src="assets/js/alerts.js"></script>

    <!-- Datatables Cdn -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Internal Datatables JS -->
    <script src="assets/js/datatables.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/custom.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            $('#datatable-basict').DataTable({
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                },
                "pageLength": 10,
                // scrollX: true
            });
            $('#datatable-basics').DataTable({
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                },
                "pageLength": 10,
                // scrollX: true
            });
            $('#datatable-basica').DataTable({
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                },
                "pageLength": 10,
                // scrollX: true
            });
            // Select All for datatable-basics
            $('#select-all').on('click', function() {
                var isChecked = $(this).is(':checked');
                $('#datatable-basics tbody input.form-check-input').prop('checked', isChecked);
            });

            // Select All for datatable-basict
            $('#select-all2').on('click', function() {
                var isChecked = $(this).is(':checked');
                $('#datatable-basict tbody input.form-check-input').prop('checked', isChecked);
            });

            // Select All for datatable-basica
            $('#select-all3').on('click', function() {
                var isChecked = $(this).is(':checked');
                $('#datatable-basica tbody input.form-check-input').prop('checked', isChecked);
            });

            // Ensure "Select All" checkbox updates when individual checkboxes are clicked
            $('#datatable-basict tbody').on('change', 'input.form-check-input', function() {
                if ($('input.form-check-input:checked').length === $('input.form-check-input').length) {
                    $('#select-all').prop('checked', true);
                } else {
                    $('#select-all').prop('checked', false);
                }
            });

            $('#datatable-basics tbody').on('change', 'input.form-check-input', function() {
                if ($('input.form-check-input:checked').length === $('input.form-check-input').length) {
                    $('#select-all').prop('checked', true);
                } else {
                    $('#select-all').prop('checked', false);
                }
            });

            $('#datatable-basica tbody').on('change', 'input.form-check-input', function() {
                if ($('input.form-check-input:checked').length === $('input.form-check-input').length) {
                    $('#select-all').prop('checked', true);
                } else {
                    $('#select-all').prop('checked', false);
                }
            });

            // Handle Send Mail button click
            $('.send-mail').on('click', function() {
                let selectedCustomers = [];
                $('input.form-check-input:checked').each(function() {
                    selectedCustomers.push($(this).val());
                });

                if (selectedCustomers.length === 0) {
                    toastr.error('Please select at least one customer.');
                    return;
                }

                // Populate the mail template modal with the selected customer data
                $('#mailTemplateModal').modal('show');
            });

            $('.send-mail-primary').on('click', function() {
                let selectedCustomers = [];
                $('input.form-check-input:checked').each(function() {
                    selectedCustomers.push({
                        customer_id: $(this).val(),
                        secondary_name: $(this).data('secondary-name')
                    });
                });
                console.log(selectedCustomers, "ssaa");
                if (selectedCustomers.length === 0) {
                    toastr.error('Please select at least one customer.');
                    return;
                }

                // Populate the mail template modal with the selected customer data
                $('#mailPrimaryTemplateModal').modal('show');
            });

            // Handle mail template form submission
            $('#mailTemplateForm').on('submit', function(e) {
                e.preventDefault();
                let selectedCustomers = [];
                $('input.form-check-input:checked').each(function() {
                    selectedCustomers.push($(this).val());
                });
                console.log(selectedCustomers, "ss");


                let formData = $(this).serializeArray();
                let template = formData.find(field => field.name === 'mail_template').value;

                $.ajax({
                    url: "{{ route('send.mail') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_ids: selectedCustomers,
                        mail_template: template
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        $('#mailTemplateModal').modal('hide');
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while sending emails.');
                    }
                });
            });

            $('#mailPrimaryTemplateForm').on('submit', function(e) {
                e.preventDefault();
                let selectedCustomers = [];
                $('input.form-check-input:checked').each(function() {
                    selectedCustomers.push({
                        customer_id: $(this).val(),
                        secondary_name: $(this).data('secondary-name')
                    });
                });
                console.log(selectedCustomers, "ss");


                let formData = $(this).serializeArray();
                let template = formData.find(field => field.name === 'mail_template').value;

                $.ajax({
                    url: "{{ route('send.mail.primary') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_ids: selectedCustomers,
                        mail_template: template
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        $('#mailPrimaryTemplateModal').modal('hide');
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while sending emails.');
                    }
                });
            });

            // Handle Send WhatsApp button click
            $('.send-whatsapp').on('click', function() {
                let selectedCustomers = [];
                $('input.form-check-input:checked').each(function() {
                    selectedCustomers.push($(this).val());
                });

                if (selectedCustomers.length === 0) {
                    alert('Please select at least one customer.');
                    return;
                }

                // Store selected customers for use in the form submission
                $('#whatsappTemplateForm').data('selectedCustomers', selectedCustomers);

                // Open the WhatsApp template modal
                $('#whatsappTemplateModal').modal('show');
            });

            $('.send-whatsapp-primary').on('click', function() {
                let selectedCustomers = [];
                $('input.form-check-input:checked').each(function() {
                    selectedCustomers.push({
                        customer_id: $(this).val(),
                        secondary_name: $(this).data('secondary-name')
                    });
                });

                if (selectedCustomers.length === 0) {
                    alert('Please select at least one customer.');
                    return;
                }

                // Store selected customers for use in the form submission
                $('#whatsappPrimaryTemplateForm').data('selectedCustomers', selectedCustomers);

                // Open the WhatsApp template modal
                $('#whatsappPrimaryTemplateModal').modal('show');
            });

            $('#whatsappTemplateForm').on('submit', function(e) {
                e.preventDefault();

                let selectedCustomers = $(this).data('selectedCustomers'); // Retrieve selected customers
                let formData = $(this).serializeArray();
                let template = formData.find(field => field.name === 'whatsapp_template').value;

                // Send the WhatsApp messages via AJAX to the backend
                $.ajax({
                    url: "{{ route('send.whatsapp') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_ids: selectedCustomers,
                        event: template
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        $('#whatsappTemplateModal').modal('hide');
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while sending WhatsApp messages.');
                    }
                });
            });

            $('#whatsappPrimaryTemplateForm').on('submit', function(e) {
                e.preventDefault();

                let selectedCustomers = $(this).data('selectedCustomers'); // Retrieve selected customers
                let formData = $(this).serializeArray();
                let template = formData.find(field => field.name === 'whatsapp_template').value;

                // Send the WhatsApp messages via AJAX to the backend
                $.ajax({
                    url: "{{ route('send.whatsapp.primary') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        customer_ids: selectedCustomers,
                        event: template
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                        $('#whatsappPrimaryTemplateModal').modal('hide');
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while sending WhatsApp messages.');
                    }
                });
            });

        });
    </script>
@endsection
