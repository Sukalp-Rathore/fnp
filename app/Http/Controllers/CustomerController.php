<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CommonEvent;
class CustomerController extends Controller
{
    //
    public function Customers()
    {
        $customers = Customer::all();
        $e = CommonEvent::get();
        $events = $e[0]->events;
        return view('customers' , compact('customers','events'));
    }

    public function createCustomer(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|',
            'customer_address' => 'nullable|string',
            'customer_type' => 'required|string',
            'customer_event' => 'nullable|string',
        ]);

        $existing = Customer::where('customer_email', $request->customer_email)->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Customer already exists']);
        }
        $existing = Customer::where('customer_phone', $request->customer_phone)->first();   
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Customer already exists']);
        }
        Customer::create([
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'customer_type' => $request->customer_type,
            'customer_event' => $request->customer_event,
        ]);
        return response()->json(['success' => true, 'message' => 'Customer created successfully']);
    }

    public function showEditCustomer(Request $request)
    {   
        $request->validate([
            'customerId' => 'required',
        ]);
        $customerId = $request->input('customerId');
        $customer = Customer::where('_id', $customerId)->first();
        return view('edit-customer', compact('customer'));
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
