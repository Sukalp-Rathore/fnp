<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Models\Customer;
use Illuminate\Pagination\Paginator;

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
            $columns = ['customer_name', 'customer_email', 'customer_phone', 'customer_type', 'created_at'];
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

        $customers = Customer::whereIn('_id', $request->customer_ids)->get();

        foreach ($customers as $customer) {
            $details = [
                'subject' => 'Notification Email',
                'title' => 'Hello ' . $customer->customer_name,
                'body' => 'This is a notification email.'
            ];

            // Dynamically reference the selected mail template from the Emails folder
            $templatePath = 'Emails.' . $request->mail_template;

            Mail::send($templatePath, ['details' => $details], function ($message) use ($customer, $details) {
                $message->to($customer->customer_email)
                        ->subject($details['subject']);
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
