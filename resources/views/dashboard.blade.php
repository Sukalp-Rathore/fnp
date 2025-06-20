@extends('layout')

@section('content')
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
                <div class="card-body p-0 pt-1">

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
                                                <span class="badge bg-primary">{{ $f->event_date }}</span>
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
                <div class="card-body p-0 pt-1">
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
                                                    <div class="fw-medium">{{ $f->vendor }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $f->total_revenue }}</span>
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
        <div class="col-xl-6">
            <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                    <div class="card-title" style="font-size:0.85rem !important">
                        Customers who Orders Last Year For Upcoming Event
                    </div>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-secondary btn-sm">Send SMS</a>
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm">Whatsapp</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (sizeof($customers) == 0)
                        <div class="alert alert-primary" role="alert">
                            No Customers Found!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="datatable-basic" class="table text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <input class="form-check-input" type="checkbox" id="checkboxNoLabel1"
                                                value="" aria-label="...">
                                        </th>
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
                                                    value="" aria-label="..." checked>
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

        <div class="col-xl-6">
            <div class="card custom-card overflow-hidden">
                <div class="card-header justify-content-between">
                    <div class="card-title" style="font-size:0.85rem !important">
                        Customers With Upcoming Event(15days)
                    </div>
                    <div>
                        <a href="javascript:void(0);" class="btn btn-secondary btn-sm">Send SMS</a>
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm">Whatsapp</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (sizeof($customersUp) == 0)
                        <div class="alert alert-primary" role="alert">
                            No Customers Found!
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="datatable-basic" class="table text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <input class="form-check-input" type="checkbox" id="checkboxNoLabel1"
                                                value="" aria-label="...">
                                        </th>
                                        <th>Customer Name</th>
                                        <th>Customer Phone</th>
                                        <th>Customer Email</th>
                                        <th>Sender Name</th>
                                        <th>Customer Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customersUp as $c)
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" id="checkboxNoLabel02"
                                                    value="" aria-label="..." checked>
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
    </div>








    {{-- 

    <div class="row mb-4">
        <div class="col-md-12">
            <form method="GET" action="{{ route('dashboard') }}">
                <input type="hidden" name="filter_by_this_month" value="true">
                <button type="submit" class="btn btn-primary">Show This Month</button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- KPI Cards -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text">{{ $totalSales }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Purchase</h5>
                    <p class="card-text">{{ $totalPurchase }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Bouquets</h5>
                    <p class="card-text">{{ $totalBouquets }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Pending Orders</h5>
                    <p class="card-text">{{ $totalPendingOrders }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Vendors by Revenue -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h5>Top Vendors by Revenue</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topVendors as $vendor)
                        <tr>
                            <td>{{ $vendor->vendor }}</td>
                            <td>{{ $vendor->total_revenue }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div> --}}
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
@endsection
