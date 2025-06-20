<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory;

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
            $customer_name = trim($row[2] ?? '');
            $customer_address = trim($row[3] ?? '');
            $event_name = trim($row[6] ?? '');
            $sender_name = trim($row[7] ?? '');
            $customer_phone = trim($row[4] ?? '');
            $event_date = trim($row[1] ?? '');

            $data = [
                'customer_name' => $customer_name,
                'customer_address' => $customer_address,
                'event_name' => $event_name,
                'sender_name' => $sender_name,
                'customer_phone' => $customer_phone,
                'customer_type' => 'secondary',
                'event_date' => $event_date,
                'created_by' => 'sale excel 2',
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

    public function importData(Request $request)
    {
        // Fetch all records from the 'test' collection directly using MongoDB
        $testRecords = DB::connection('mongodb')->table('test')->get();

        foreach ($testRecords as $record) {
            // Convert role
            $customer_type = 'secondary';

            // Build and save new customer
            Customer::create([
                'customer_type'     => $customer_type,
                'customer_name'     => $record->customer_name,
                'vendor_email'    => $record->to_email ?? null,
                'customer_address'  => $record->address ?? null,
                'customer_phone'    => $record->mobile ?? null,
                'event_name' => $record->events ?? null,
                'product' => $record->product ?? null,
                'product_value' => $record->product_value ?? null,
                'message' => $record->message ?? null,
                'forward_date' => $record->forward_date ?? null,
                'created_by' => 'forward event order cpanel',
                'created_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Customers imported successfully.']);
    }

    public function findDuplicateCustomers()
    {
        // Fetch all customers where at least one of the key fields is not null
        $customers = Customer::where(function($q) {
            $q->whereNotNull('customer_name')
              ->orWhereNotNull('customer_email')
              ->orWhereNotNull('customer_phone');
        })->get();
    
        $duplicates = [
            'by_name' => [],
            'by_email' => [],
            'by_phone' => [],
            'by_all_fields' => []
        ];
    
        // Group by normalized customer_name
        $byName = $customers->filter(function ($c) {
            return $c->customer_name !== null;
        })->groupBy(function ($c) {
            return strtolower(trim($c->customer_name));
        });
        foreach ($byName as $group) {
            if ($group->count() > 1) {
                $duplicates['by_name'][] = $group;
            }
        }
    
        // Group by normalized customer_email
        $byEmail = $customers->filter(function ($c) {
            return $c->customer_email !== null;
        })->groupBy(function ($c) {
            return strtolower(trim($c->customer_email));
        });
        foreach ($byEmail as $group) {
            if ($group->count() > 1) {
                $duplicates['by_email'][] = $group;
            }
        }
    
        // Group by normalized customer_phone
        $byPhone = $customers->filter(function ($c) {
            return $c->customer_phone !== null;
        })->groupBy(function ($c) {
            return preg_replace('/\D/', '', $c->customer_phone);
        });
        foreach ($byPhone as $group) {
            if ($group->count() > 1) {
                $duplicates['by_phone'][] = $group;
            }
        }
    
        // Group by all 3 fields combined (only where all are present)
        $byAllFields = $customers->filter(function ($c) {
            return $c->customer_name !== null && $c->customer_email !== null && $c->customer_phone !== null;
        })->groupBy(function ($c) {
            return strtolower(trim($c->customer_name)) . '|' .
                   strtolower(trim($c->customer_email)) . '|' .
                   preg_replace('/\D/', '', $c->customer_phone);
        });
        foreach ($byAllFields as $group) {
            if ($group->count() > 1) {
                $duplicates['by_all_fields'][] = $group;
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Duplicate customers found.',
            'duplicates' => $duplicates,
        ]);
    }
    
    
    

    public function deleteDuplicateCustomers()
    {
        // Fetch all customers where at least one of the key fields is not null
        $customers = Customer::where(function($q) {
            $q->whereNotNull('customer_name')
              ->orWhereNotNull('customer_email')
              ->orWhereNotNull('customer_phone');
        })->get();
    
        $deletedCount = 0;
        $deletedIds = [];
    
        $groupAndDelete = function ($grouped) use (&$deletedCount, &$deletedIds) {
            foreach ($grouped as $group) {
                if ($group->count() > 1) {
                    $groupToDelete = $group->slice(1); // Keep the first one
                    foreach ($groupToDelete as $dup) {
                        $deletedIds[] = $dup->_id;
                        $dup->delete();
                        $deletedCount++;
                    }
                }
            }
        };
    
        // Group by normalized customer_name
        $byName = $customers->filter(fn($c) => $c->customer_name !== null)
            ->groupBy(fn($c) => strtolower(trim($c->customer_name)));
        $groupAndDelete($byName);
    
        // Group by normalized customer_email
        $byEmail = $customers->filter(fn($c) => $c->customer_email !== null)
            ->groupBy(fn($c) => strtolower(trim($c->customer_email)));
        $groupAndDelete($byEmail);
    
        // Group by normalized customer_phone
        $byPhone = $customers->filter(fn($c) => $c->customer_phone !== null)
            ->groupBy(fn($c) => preg_replace('/\D/', '', $c->customer_phone));
        $groupAndDelete($byPhone);
    
        // Group by all 3 fields combined (only where all are present)
        $byAllFields = $customers->filter(fn($c) =>
            $c->customer_name !== null &&
            $c->customer_email !== null &&
            $c->customer_phone !== null
        )->groupBy(function ($c) {
            return strtolower(trim($c->customer_name)) . '|' .
                   strtolower(trim($c->customer_email)) . '|' .
                   preg_replace('/\D/', '', $c->customer_phone);
        });
        $groupAndDelete($byAllFields);
    
        return response()->json([
            'success' => true,
            'message' => "$deletedCount duplicate customer(s) deleted successfully.",
            'deleted_ids' => $deletedIds,
        ]);
    }

    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        // Read the uploaded CSV file
        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));

        // Skip the header row
        $header = array_shift($csvData);
        $created = 0;
        foreach ($csvData as $row) {
            $flowerName = $row[1]; // Column 1: flower_name
            $color = $row[2];      // Column 2: color
            $sellingPrice = $row[3]; // Column 3: selling_price
            // Generate the image file name
            // Generate the image file name without replacing spaces or converting to lowercase
            $imageFileName = $flowerName . ' ' . $color . '.png';
            // dd($imageFileName);
            // Path to the image in the public folder
            $imagePath = public_path('assets/images/flowers/' . $imageFileName);
            // dd($imagePath);
            // Convert the image to base64 if it exists
            $productImage = null;
            if (file_exists($imagePath)) {
                $imageData = file_get_contents($imagePath);
                $mimeType = mime_content_type($imagePath); // Get the MIME type of the image
                $productImage = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
            }
            // dd($productImage);
            // Create an entry in the Inventory model
            Inventory::create([
                'product_name' => $flowerName,
                'color' => $color,
                'selling_price' => (int)$sellingPrice,
                'product_image' => $productImage,
            ]);
            $created++; 
        }
        return response()->json(['success' => true, 'message' => " $created CSV uploaded and inventory updated successfully"]);
    }
    
}
