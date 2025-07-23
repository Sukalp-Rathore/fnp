<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\CommonEvent;
use App\Models\Vendor;
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Http;
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
        $request->merge(['order_status' => "pending"]);

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
                    'name' => $vendor->first_name,
                    'order_details' => [
                        'customer_name_primary' => $request->customer_name_primary,
                        'customer_email_primary' => $request->customer_email_primary,
                        'customer_mobile_primary' => $request->customer_mobile_primary,
                        'customer_address' => $request->customer_address,
                        'city' => $request->city,
                        'event_name' => $request->event_name,
                        'delivery_date' => $request->delivery_date,
                        'products' => $request->products,
                        'value' => $request->value,
                        'sender_name' => $request->customer_name_secondary ?: 'N/A',
                        'sender_email' => $request->customer_email_secondary ?: 'N/A',
                        'sender_mobile' => $request->customer_mobile_secondary ?: 'N/A',
                    ],
                ];
                $vendor->email = "sukalprathore@gmail.com";
                // Mail::send('Emails.vendor', ['details' => $details], function ($message) use ($vendor) {
                //     $message->to($vendor->email)
                //             ->subject('New Order Assigned - Flowers n Petals');
                // });

                $toAndComponents = [];
                $phone = preg_replace('/\D/', '', $vendor->mobile); // Remove non-numeric characters
                $formattedPhone = strlen($phone) === 10 ? '91' . $phone : $phone; // Add '91' if length is 10
                $toAndComponents[] = [
                    "to" => [$formattedPhone],
                    "components" => [
                        "body_1" => [
                            "type" => "text",
                            "value" => $request->customer_name_primary // Dynamically send customer_name
                        ],
                        "body_2" => [
                            "type" => "text",
                            "value" => $request->customer_email_primary // Dynamically send customer_name
                        ],
                        "body_3" => [
                            "type" => "text",
                            "value" => $request->customer_mobile_primary // Dynamically send customer_name
                        ],
                        "body_4" => [
                            "type" => "text",
                            "value" => $request->customer_address // Dynamically send customer_name
                        ],
                        "body_5" => [
                            "type" => "text",
                            "value" => $request->city // Dynamically send customer_name
                        ],
                        "body_6" => [
                            "type" => "text",
                            "value" => $request->delivery_date // Dynamically send customer_name
                        ],
                        "body_7" => [
                            "type" => "text",
                            "value" => $request->products // Dynamically send customer_name
                        ],
                        "body_8" => [
                            "type" => "text",
                            "value" => $request->sender_name // Dynamically send customer_name
                        ]
                    ]
                ];
            
                $payload = [
                    "integrated_number" => "918109535634",
                    "content_type" => "template",
                    "payload" => [
                        "messaging_product" => "whatsapp",
                        "type" => "template",
                        "template" => [
                            "name" => "vendor",
                            "language" => [
                                "code" => "en",
                                "policy" => "deterministic"
                            ],
                            "namespace" => "a6f0d3b7_77f1_463a_94dd_a8c9f5054401",
                            "to_and_components" => $toAndComponents
                        ]
                    ]
                ];
            
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'authkey' => '451815AXQYneFUH686786fbP1' // Replace with your actual authkey
                ])->post('https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/', $payload);

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
