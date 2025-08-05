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
use App\Models\Vendor;

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
        // dd($customersWithFutureEvents);
        // KPI3 
        // Fetch the latest upcoming event
        $latestUpcomingEvent = FestivalEvent::where('event_date', '>=', Carbon::now())
            ->orderBy('event_date', 'asc')
            ->first();
        // dd($latestUpcomingEvent);
        // Fetch all customers with the same event_name
        $customers = Customer::where('event_name', $latestUpcomingEvent->events)->get();
        // dd($customers);

        // Fetch customers whose event_date is within the next 15 days
        // Fetch customers whose event_date is within the next 15 days
        $today = Carbon::now();
        $endDate = $today->copy()->addDays(3);

        // Get all customers
        $customerst = Customer::whereNotNull('event_date')->get();

        // Filter manually based on month & day
        $customersUp = $customerst->filter(function ($customer) use ($today, $endDate) {
            $event = Carbon::parse(getutc($customer->event_date))->setYear($today->year);

            return $event->between($today, $endDate);
        });

        $primaryWithUpcomingSecondary = Customer::where('customer_type', 'primary')
        ->whereNotNull('secondary_customers')
        ->get()
        ->flatMap(function ($primary) use ($today, $endDate) {
            if (is_array($primary->secondary_customers) && count($primary->secondary_customers)) {
                return collect($primary->secondary_customers)->filter(function ($secondary) use ($today, $endDate) {
                    if (!empty($secondary['event_date'])) {
                        $eventDate = Carbon::parse($secondary['event_date'])->setYear($today->year);
                        return $eventDate->between($today, $endDate);
                    }
                    return false;
                })->map(function ($secondary) use ($primary) {
                    return [
                        'primary_id' => $primary->_id,
                        'primary_name' => $primary->customer_name,
                        'primary_email' => $primary->customer_email,
                        'primary_phone' => $primary->customer_phone,
                        'secondary_name' => $secondary['customer_name'] ?? '',
                        'event_name' => $secondary['event_name'] ?? '',
                        'event_date' => $secondary['event_date'] ?? '',
                    ];
                });
            }
            return [];
        });
        // dd($primaryWithUpcomingSecondary);
        
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
                    $a = Vendor::find($vendor);
                    if (!$a) {
                        return null; // Skip if vendor not found
                    } 
                    return [
                        'vendor' => $a->first_name,
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
                    $a = Vendor::find($vendor);
                    if (!$a) {
                        return null; // Skip if vendor not found
                    } 
                    return [
                        'vendor' => $a->first_name,
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
            'customersUp',
            'primaryWithUpcomingSecondary'
        ));
    }
}
