<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class DataController extends Controller
{
    //
    public function testForm()
    {
        return view('test-form');
    }

    public function addCustomer(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);
    
        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');
    
        $header = fgetcsv($file); // skip header row
        $created = 0;
        $rowNumber = 2;

        while (($row = fgetcsv($file)) !== false) {
            $customer_name = trim($row[1] ?? '');
            $customer_address = trim($row[2] ?? '');
            $event_name = trim($row[3] ?? '');
            $sender_name = trim($row[4] ?? '');
            $customer_phone = trim($row[5] ?? '');

            $data = [
                'customer_name' => $customer_name,
                'customer_address' => $customer_address,
                'event_name' => $event_name,
                'sender_name' => $sender_name,
                'customer_phone' => $customer_phone,
                'created_by' => 'sale excel',
            ];

            $insert = Customer::insert($data);
            if ($insert) {
                $created++;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Error inserting row $rowNumber"
                ]);
            }
            $rowNumber++;
        }
        fclose($file);
        return response()->json([
            'status' => 'success',
            'message' => "$created customers added successfully"
        ]);
    }

    public function customerType(Request $request)
    {
        $customers = Customer::all();
        $upadated = 0;
        foreach ($customers as $customer) {
            if (empty($customer->customer_type)) {
                $customer->customer_type = 'secondary';
                $customer->save();
                $upadated++;
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => "$upadated customers updated successfully"
        ]);
    }
}
