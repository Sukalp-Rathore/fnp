<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Bouquet;
use App\Models\CommonEvent;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\FestivalEvent;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    public function dashboard(Request $request)
    {

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
    
        // KPI 1: Fetch future events in the current month from FestivalEvent
        $futureEventsInCurrentMonth = FestivalEvent::whereMonth('event_date', $currentMonth)
            ->whereYear('event_date', $currentYear)
            ->where('event_date', '>=', Carbon::now()) // Only future events
            ->get();
        // dd($futureEventsInCurrentMonth);   

        // KPI 2: Fetch customers with event_date in the current month (excluding past days)
        $customersWithFutureEvents = Customer::whereMonth('event_date', $currentMonth)
            ->whereYear('event_date', $currentYear)
            ->where('event_date', '>=', Carbon::now()) // Only future events
            ->get();

        // KPI3 
        // Fetch the latest upcoming event
        $latestUpcomingEvent = FestivalEvent::where('event_date', '>=', Carbon::now())
            ->orderBy('event_date', 'asc')
            ->first();

        // Fetch all customers with the same event_name
        $customers = Customer::where('event_name', $latestUpcomingEvent->events)->get();
        // dd($customers);

        // Fetch customers whose event_date is within the next 15 days
        $customersUp = Customer::whereBetween('event_date', [
            Carbon::now(),
            Carbon::now()->addDays(15)
        ])->get();

        
        // Check if the user selected "This Month"
        $filterByThisMonth = $request->input('filter_by_this_month', false);

        if ($filterByThisMonth) {
            // Filter KPIs for the current month
            $selectedMonth = Carbon::now()->month;
            $selectedYear = Carbon::now()->year;

            $totalSales = Sale::whereMonth('created_at', $selectedMonth)
                ->whereYear('created_at', $selectedYear)
                ->sum('total_sale');

            $totalPurchase = Purchase::whereMonth('created_at', $selectedMonth)
                ->whereYear('created_at', $selectedYear)
                ->sum('amount');

            $totalBouquets = Bouquet::whereMonth('created_at', $selectedMonth)
                ->whereYear('created_at', $selectedYear)
                ->count();

            $totalPendingOrders = Order::where('order_status', 'pending')
                ->whereMonth('created_at', $selectedMonth)
                ->whereYear('created_at', $selectedYear)
                ->count();
            // Calculate Top Vendors by Revenue (only if $vendor is present)
            $topVendors = Order::whereNotNull('vendor') // Ensure vendor is present
                ->whereMonth('created_at', $selectedMonth)
                ->whereYear('created_at', $selectedYear)
                ->get()
                ->groupBy('vendor')
                ->map(function ($orders, $vendor) {
                    return [
                        'vendor' => $vendor,
                        'total_revenue' => $orders->sum('order_price'),
                    ];
                })
                ->sortByDesc('total_revenue')
                ->take(5);
        } else {
            // Default KPIs (all-time data)
            $totalSales = Sale::sum('total_sale');
            $totalPurchase = Purchase::sum('amount');
            $totalBouquets = Bouquet::count();
            $totalPendingOrders = Order::where('order_status', 'pending')->count();
            $topVendors = Order::whereNotNull('vendor') // Ensure vendor is present
                ->get()
                ->groupBy('vendor')
                ->map(function ($orders, $vendor) {
                    return [
                        'vendor' => $vendor,
                        'total_revenue' => $orders->sum('order_price'),
                    ];
                })
                ->sortByDesc('total_revenue')
                ->take(5);
        }
        return view('dashboard', compact(
            'totalSales',
            'totalPurchase',
            'totalBouquets',
            'totalPendingOrders',
            'futureEventsInCurrentMonth',
            'topVendors',
            'filterByThisMonth',
            'customers',
            'customersUp'
        ));
    }
}
