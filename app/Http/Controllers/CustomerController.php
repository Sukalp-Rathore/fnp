<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CommonEvent;
use Illuminate\Pagination\Paginator;
use App\Models\FestivalEvent;
class CustomerController extends Controller
{
    //
    public function customers(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::query();
    
            // Apply search filter if provided
            if ($request->input('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            }
    
            // Get column index and direction from request
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir', 'asc');
            
            // Map index to column name
            $columns = ['customer_name', 'customer_email', 'customer_phone', 'customer_address', 'event_name','event_date','customer_type', 'created_at'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'customer_name';
    
            // Apply sorting
            $query->orderBy($orderColumn, $orderDirection);
    
            // Get pagination info
            $length = intval($request->input('length', 10)); // Number of records per page
            $start = intval($request->input('start', 0));    // Offset
            $currentPage = ($start / $length) + 1;
    
            // Set current page for paginator
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
    
            $customers = $query->paginate($length);
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $customers->total(),
                'recordsFiltered' => $customers->total(),
                'data' => $customers->items(),
            ]);
        }
    
        // Initial load
        // Fetch events from CommonEvent
        $commonEvents = CommonEvent::first();
        $events = $commonEvents ? $commonEvents->events : [];

        // Fetch events from FestivalEvent
        $festivalEvents = FestivalEvent::pluck('events')->flatten()->toArray();

        // Merge both event arrays and remove duplicates
        $allEvents = array_unique(array_merge($events, $festivalEvents));
        // dd($allEvents);
        // Fetch all primary customers and pluck their names
        $primaryCustomerNames = Customer::where('customer_type', 'primary')->pluck('customer_name')->toArray();
        return view('customers', compact('allEvents', 'primaryCustomerNames'));
    }
    

    public function createCustomer(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:15',
            'customer_address' => 'nullable|string|max:500',
            'customer_type' => 'required|in:primary,secondary',
            'event_name' => 'nullable|string|max:255',
            'primary_customer_name' => 'nullable|string|max:255',
        ]);

        if ($request->customer_type === 'secondary') {
            // Check if the primary customer exists
            $primaryCustomer = Customer::where('customer_name', $request->primary_customer_name)->first();
    
            if ($primaryCustomer) {
                // Append this customer to the secondary_customers array in the primary customer record
                $secondaryCustomer = [
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'event_name' => $request->event_name,
                    'event_date' => $request->event_date,
                    'customer_type' => 'secondary',
                    'customer_address' => $request->customer_address,
                ];
    
                $primaryCustomer->secondary_customers = array_merge(
                    $primaryCustomer->secondary_customers ?? [],
                    [$secondaryCustomer]
                );
                $primaryCustomer->save();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Secondary customer added to the primary customer record.',
                ]);
            } else {
                // Create a new record for the secondary customer
                Customer::create([
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'customer_address' => $request->customer_address,
                    'customer_type' => 'secondary',
                    'event_name' => $request->event_name,
                    'event_date' => $request->event_date,
                ]);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Secondary customer added, but primary customer not found.',
                ]);
            }
        } else {
            // Create a new record for the primary customer
            Customer::create($request->all());
    
            return response()->json([
                'success' => true,
                'message' => 'Primary customer added successfully.',
            ]);
        }
    }

    public function showEditCustomer(Request $request)
    {   
        $request->validate([
            'customerId' => 'required',
        ]);
        $customerId = $request->input('customerId');
        $customer = Customer::where('_id', $customerId)->first();
        $// Fetch events from CommonEvent
    $commonEvents = CommonEvent::first();
    $events = $commonEvents ? $commonEvents->events : [];

    // Fetch events from FestivalEvent
    $festivalEvents = FestivalEvent::pluck('events')->flatten()->toArray();

    // Merge both event arrays and remove duplicates
    $allEvents = array_unique(array_merge($events, $festivalEvents));
    // dd($allEvents);
        return view('edit-customer', compact('customer','allEvents'));
    }

    public function updateCustomer(Request $request)
    {
        $request->validate([
            'customerId' => 'required',
            'customer_name' => 'required|string',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable',
            'customer_address' => 'nullable|string',
            'customer_type' => 'nullable|string',
            'event_name' => 'nullable|string',
        ]);
        $customerId = $request->input('customerId');
        $customer = Customer::where('_id', $customerId)->first();
        $customer->customer_name = $request->input('customer_name');
        $customer->customer_email = $request->input('customer_email');
        $customer->customer_phone = $request->input('customer_phone');
        $customer->customer_address = $request->input('customer_address');
        $customer->customer_type = $request->input('customer_type');
        $customer->event_name = $request->input('event_name');
        $customer->save();
        return response()->json(['success' => true, 'message' => 'Customer updated successfully']);
    }

    public function deleteCustomer(Request $request)
    {
        $request->validate([
            'customerId' => 'required',
        ]);
        $customerId = $request->input('customerId');
        $customer = Customer::where('_id', $customerId)->first();
        if ($customer) {
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }
    }

    public function primaryCustomers()
    {
        // Fetch all primary customers
        $customers = Customer::where('customer_type', 'primary')->get();
        return view('customer-list', compact('customers'));
    }

    public function getSecondaryCustomers(Request $request)
    {
        $request->validate([
            'customerId' => 'required',
        ]);
        $customer = Customer::find($request->customerId);
        if (!$customer || empty($customer->secondary_customers)) {
            return response()->json(['success' => false, 'message' => 'No secondary customers found for this primary customer.']);
        }
        $secondaryCustomers = $customer->secondary_customers;
        return view('show-secondary', compact('secondaryCustomers'));
    }
}
