<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Models\Customer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::query();
    
            // Apply search filter if needed
            if ($request->input('search.value')) {
                $search = $request->input('search.value');
                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            }
    
            // Handle ordering
            $columns = ['customer_name', 'customer_email', 'customer_phone', 'event_name' , 'event_date' , 'customer_type', 'created_at'];
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderColumn = $columns[$orderColumnIndex] ?? 'customer_name';
            $orderDirection = $request->input('order.0.dir', 'asc');
            $query->orderBy($orderColumn, $orderDirection);
    
            // Handle pagination
            $length = intval($request->input('length', 10));
            $start = intval($request->input('start', 0));
            $currentPage = ($start / $length) + 1;
            Paginator::currentPageResolver(fn () => $currentPage);
    
            $customers = $query->paginate($length);
    
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $customers->total(),
                'recordsFiltered' => $customers->total(),
                'data' => $customers->items(),
            ]);
        }
    
        return view('notification');
    }
    
    public function testMail()
    {
        return view('test-mail');
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'mail_template' => 'required|string',
        ]);

        // Set subject based on mail template
        $subjects = [
            'rakhi' => 'Celebrate the Bond of Love this Rakhi – Pre-Book Now 🎁',
            'diwali' => 'Light Up Their Diwali – Pre-Book Your Festive Gifts Today ✨',
            'birthday' => 'Surprise Them on Their Birthday – Book Fresh Flowers & Gifts 🎂',
            'anniversary' => 'Celebrate Love & Togetherness – Send Anniversary Surprises 💞',
            'newyear' => 'Welcome 2026 with Fresh Flowers & Celebration Hampers 🎉'
        ];

        $subject = $subjects[$request->mail_template] ?? 'Flowers n Petals – Special Offer Just for You';
        $customers = Customer::whereIn('_id', $request->customer_ids)->get();
        foreach ($customers as $customer) {
            $details = [
                'customer_name' => $customer->customer_name
            ];
            if($customer->customer_email == null) {
                continue; // Skip if email is not set
            }
            // $customer->customer_email= "sukalprathore@gmail.com";
            // Dynamically reference the selected mail template from the Emails folder
            $templatePath = 'Emails.' . $request->mail_template;
            Mail::send($templatePath, ['details' => $details], function ($message) use ($customer, $details, $subject) {
                $message->to($customer->customer_email)
                        ->subject($subject);
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Emails sent successfully.',
        ]);
    }

    public function sendWhatsapp(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'event' => 'required|string',
        ]);
    
        // Fetch customer phone numbers and names
        $customers = Customer::whereIn('_id', $request->customer_ids)->get();
    
        $toAndComponents = [];
        foreach ($customers as $customer) {
            if (empty($customer->customer_phone)) {
                continue; // Skip if phone number is not set
            }
            $phone = preg_replace('/\D/', '', $customer->customer_phone); // Remove non-numeric characters
            $formattedPhone = strlen($phone) === 10 ? '91' . $phone : $phone; // Add '91' if length is 10
            // $formattedPhone = "919340260519";
            $toAndComponents[] = [
                "to" => [$formattedPhone],
                "components" => [
                    "body_1" => [
                        "type" => "text",
                        "value" => $customer->customer_name // Dynamically send customer_name
                    ]
                ]
            ];  
        }
    
        $payload = [
            "integrated_number" => "918109535634",
            "content_type" => "template",
            "payload" => [
                "messaging_product" => "whatsapp",
                "type" => "template",
                "template" => [
                    "name" => $request->event,
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
    
        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'WhatsApp messages sent successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to send WhatsApp messages.'], $response->status());
        }
    }

}
