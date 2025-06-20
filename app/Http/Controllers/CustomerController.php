<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CommonEvent;
use Illuminate\Pagination\Paginator;
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
            $columns = ['customer_name', 'customer_email', 'customer_phone', 'customer_address', 'customer_event', 'customer_type', 'created_at'];
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
        $e = CommonEvent::get();
        $events = $e[0]->events ?? [];
    
        return view('customers', compact('events'));
    }
    

    public function createCustomer(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:15',
            'customer_address' => 'nullable|string|max:500',
            'customer_type' => 'required|in:Primary,Secondary',
            'primary_customer_name' => 'nullable|string|max:255',
        ]);
    
        if ($request->customer_type === 'Secondary') {
            // Check if the primary customer exists
            $primaryCustomer = Customer::where('customer_name', $request->primary_customer_name)->first();
    
            if ($primaryCustomer) {
                // Append this customer to the secondary_customers array in the primary customer record
                $secondaryCustomer = [
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
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
                    'customer_type' => 'Secondary',
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
        $e = CommonEvent::get();
        $events = $e[0]->events ?? [];
        return view('edit-customer', compact('customer','events'));
    }

    public function updateCustomer(Request $request)
    {
        $request->validate([
            'customerId' => 'required',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|',
            'customer_address' => 'nullable|string',
            'customer_type' => 'required|string',
            'customer_event' => 'nullable|string',
        ]);
        $customerId = $request->input('customerId');
        $customer = Customer::where('_id', $customerId)->first();
        $customer->customer_name = $request->input('customer_name');
        $customer->customer_email = $request->input('customer_email');
        $customer->customer_phone = $request->input('customer_phone');
        $customer->customer_address = $request->input('customer_address');
        $customer->customer_type = $request->input('customer_type');
        $customer->customer_event = $request->input('customer_event');
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
}
