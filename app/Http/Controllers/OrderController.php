<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\CommonEvent;
use App\Models\Vendor;
use App\Models\Customer;

class OrderController extends Controller
{
    //
    public function allOrders()
    {
        // Logic to fetch all orders
        $orders = Order::get();
        $e = CommonEvent::get();
        $events = $e[0]->events;

        $vendors = Vendor::get();
        return view('orders' , compact('orders','events','vendors'));
    }

    public function getVendorsByCity(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
        ]);
    
        $vendors = Vendor::where('city', $request->city)->get();
    
        return response()->json([
            'success' => true,
            'vendors' => $vendors,
        ]);
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:primary,secondary',
            'customer_name_primary' => 'required|string|max:255',
            'customer_email_primary' => 'required|email|max:255',
            'customer_mobile_primary' => 'required|string|max:15',
            'customer_address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'event_name' => 'required|string|max:255',
            'delivery_date' => 'required|date',
            'products' => 'required|string',
            'customer_name_secondary' => 'nullable|string|max:255',
            'customer_email_secondary' => 'nullable|email|max:255',
            'customer_mobile_secondary' => 'nullable|string|max:15',
            'vendor' => 'nullable|string',
            'order_status' => 'pending',
            'created_by' => 'required|string|max:255',
        ]);
    
        // Create the order
        $order = Order::create($request->all());
    
        // Check if primary customer exists
        $primaryCustomer = Customer::where('customer_name', $request->customer_name_primary)
            ->orWhere('customer_email', $request->customer_email_primary)
            ->orWhere('customer_phone', $request->customer_mobile_primary)
            ->first();
    
        if (!$primaryCustomer) {
            // Create primary customer
            $primaryCustomer = Customer::create([
                'customer_name' => $request->customer_name_primary,
                'customer_email' => $request->customer_email_primary,
                'customer_phone' => $request->customer_mobile_primary,
                'customer_address' => $request->customer_address,
                'event_name' => $request->event_name,
                'customer_type' => 'primary',
            ]);
        }
    
        // If order type is secondary, check if secondary customer exists
        if ($request->order_type === 'secondary') {
            $secondaryCustomer = Customer::where('customer_name', $request->customer_name_secondary)
                ->orWhere('customer_email', $request->customer_email_secondary)
                ->orWhere('customer_phone', $request->customer_mobile_secondary)
                ->first();
    
            if (!$secondaryCustomer) {
                // Create secondary customer
                $secondaryCustomer = Customer::create([
                    'customer_name' => $request->customer_name_secondary,
                    'customer_email' => $request->customer_email_secondary,
                    'customer_phone' => $request->customer_mobile_secondary,
                    'customer_address' => $request->customer_address,
                    'event_name' => $request->event_name,
                    'customer_type' => 'secondary',
                ]);
            }
    
            // Append secondary customer to the primary customer's record
            $primaryCustomer->secondary_customers = array_merge(
                $primaryCustomer->secondary_customers ?? [],
                [$secondaryCustomer->toArray()]
            );
            $primaryCustomer->save();
        }
    
        // Check if a vendor is assigned
        if ($request->vendor) {
            $vendor = Vendor::find($request->vendor);
    
            if ($vendor) {
                // Prepare email details
                $details = [
                    'subject' => 'New Order Assigned',
                    'title' => 'Hello ' . $vendor->first_name,
                    'body' => 'You have been assigned a new order. Here are the details:',
                    'order_details' => [
                        'Customer Name (Primary)' => $request->customer_name_primary,
                        'Customer Email (Primary)' => $request->customer_email_primary,
                        'Customer Mobile (Primary)' => $request->customer_mobile_primary,
                        'Customer Address' => $request->customer_address,
                        'City' => $request->city,
                        'Event Name' => $request->event_name,
                        'Delivery Date' => $request->delivery_date,
                        'Products' => $request->products,
                    ],
                ];
    
                // Send email to vendor
                Mail::to($vendor->email)->send(new SendMail($details));
    
                // Update order status to "assigned"
                $order->update(['order_status' => 'assigned']);
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully.',
        ]);
    }

    public function markDelivered(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->order_status = 'delivered';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order marked as delivered successfully.',
        ]);
    }
}
