<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Models\Customer;

class NotificationController extends Controller
{
    //
    public function index()
    {
        $customers = Customer::all();
        return view('notification' , compact('customers'));
    }
    
    public function testMail()
    {
        return view('test-mail');
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $customers = Customer::whereIn('id', $request->customer_ids)->get();

        foreach ($customers as $customer) {
            $details = [
                'subject' => 'Notification',
                'title' => 'Hello ' . $customer->customer_name,
                'body' => 'This is a notification email.',
            ];

            Mail::to($customer->customer_email)->send(new SendMail($details));
        }

        return response()->json([
            'success' => true,
            'message' => 'Emails sent successfully to the selected customers.',
        ]);
    }

    public function sendWhatsapp(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $customers = Customer::whereIn('id', $request->customer_ids)->get();

        foreach ($customers as $customer) {
            // send whatsapp messages from here 
        }

        return response()->json([
            'success' => true,
            'message' => 'Whatsapp Messages sent successfully to the selected customers.',
        ]);
    }

    public function sendSms(Request $request)
    {
        $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $customers = Customer::whereIn('id', $request->customer_ids)->get();

        foreach ($customers as $customer) {
            // send SMS messages from here 
        }

        return response()->json([
            'success' => true,
            'message' => 'SMS Messages sent successfully to the selected customers.',
        ]);
    }
}
