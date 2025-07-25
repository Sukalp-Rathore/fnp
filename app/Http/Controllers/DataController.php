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
                    // Sort group by created_at DESC, keep the most recent one
                    $sorted = $group->sortByDesc('created_at')->values();
                    $groupToDelete = $sorted->slice(1); // Skip the newest
    
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

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData);

        $created = 0;
        $updated = 0;

        foreach ($csvData as $row) {
            $customerName = trim($row[2] ?? '');
            if ($customerName === '') continue;

            $customerAddress = trim($row[3] ?? '');

            $rawPhone = trim($row[4] ?? '');
            $firstPhone = preg_split('/[,\/]/', $rawPhone)[0] ?? '';
            $customerPhone = preg_replace('/\D/', '', $firstPhone);
            if ($customerPhone === '') continue;

            $eventName = trim($row[5] ?? '');
            $senderName = trim($row[6] ?? '');
            $eventDate = trim($row[1] ?? '');

            // Case-insensitive name match using MongoDB regex
            $existingCustomer = Customer::where('customer_name', 'regexp', new \MongoDB\BSON\Regex('^' . preg_quote($customerName, '/') . '$', 'i'))
                ->first();

            if ($existingCustomer) {
                $updatedFields = false;

                if (empty($existingCustomer->event_date) && $eventDate) {
                    $existingCustomer->event_date = $eventDate;
                    $updatedFields = true;
                }

                if (empty($existingCustomer->event_name) && $eventName) {
                    $existingCustomer->event_name = $eventName;
                    $updatedFields = true;
                }

                if ($updatedFields) {
                    $existingCustomer->save();
                    $updated++;
                }

            } else {
                Customer::create([
                    'customer_name' => $customerName,
                    'customer_address' => $customerAddress,
                    'customer_phone' => $customerPhone,
                    'event_name' => $eventName,
                    'sender_name' => $senderName,
                    'event_date' => $eventDate,
                    'created_by' => 'CSV Upload June Dec',
                ]);
                $created++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$created customers created, $updated customers updated based on customer_name.",
        ]);
    }

    public function updateDates(Request $request)
    {
        $customers = Customer::all();
        $updated = 0;
        foreach ($customers as $customer) {
            try {
                if($customer->event_date == null || $customer->event_date == '') {
                    continue; // Skip if event_date is null
                }
                // Convert event_date to UTC format
                $eventDate = setutc($customer->event_date);
                if ($eventDate) {
                    // Update the event_date field in the database
                    $customer->event_date = $eventDate;
                    $customer->save();
                    $updated++;
                }
            } catch (\Exception $e) {
                // Log or handle errors
                \Log::error("Failed to update event_date for customer ID: {$customer->_id}");
            }
        }
        return response()->json(['success' => true, 'message' => "$updated Event dates updated to UTC format successfully"]);
    }

    // public function uploadCsv(Request $request)
    // {
    //     $request->validate([
    //         'csv_file' => 'required|file|mimes:csv,txt',
    //     ]);
    
    //     $file = $request->file('csv_file');
    //     $csvData = array_map('str_getcsv', file($file->getRealPath()));
    //     $header = array_shift($csvData); // skip header
    
    //     $created = 0;
    //     $updated = 0;
    
    //     foreach ($csvData as $row) {
    //         $customerName = trim($row[2] ?? '');
    //         $customerAddress = trim($row[3] ?? '');
    //         $rawPhone = trim($row[4] ?? '');
    //         $eventName = trim($row[5] ?? '');
    //         $senderName = trim($row[6] ?? '');
    //         $eventDate = trim($row[1] ?? '');
    
    //         if ($customerName === '') continue;
    
    //         $firstPhone = preg_split('/[,\/]/', $rawPhone)[0] ?? '';
    //         $customerPhone = preg_replace('/\D/', '', $firstPhone);
    
    //         // ✅ Normalized key for exact duplicate detection
    //         $normalizedKey = strtolower($customerName) . '|' . $customerPhone;
    
    //         // Check if any record already exists with same normalized name and phone
    //         $existingCustomer = Customer::all()->filter(function ($c) use ($normalizedKey) {
    //             $existingKey = strtolower(trim($c->customer_name)) . '|' . preg_replace('/\D/', '', $c->customer_phone);
    //             return $existingKey === $normalizedKey;
    //         })->first();
    
    //         if ($existingCustomer) {
    //             $existingCustomer->event_date = $eventDate;
    //             $existingCustomer->save();
    //             $updated++;
    //         } else {
    //             Customer::create([
    //                 'customer_name' => $customerName,
    //                 'customer_address' => $customerAddress,
    //                 'customer_phone' => $customerPhone,
    //                 'event_name' => $eventName,
    //                 'sender_name' => $senderName,
    //                 'event_date' => $eventDate,
    //                 'created_by' => 'CSV Upload 23 june feb',
    //             ]);
    //             $created++;
    //         }
    //     }
    
    //     return response()->json([
    //         'success' => true,
    //         'message' => "$created customers created, $updated customers updated successfully.",
    //     ]);
    // }
    
}
