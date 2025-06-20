<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Bouquet;
use Illuminate\Support\Facades\Validator;

class BouquetController extends Controller
{
    //
    public function bouquet()
    {
        $bouquets = Bouquet::orderBy('created_at', 'desc')->get();
        return view('bouquet' , compact('bouquets'));
    }

    public function fetchInventory()
    {
        $inventory = Inventory::all(); // Fetch all inventory items
        return view('bouquet-inventory-table', compact('inventory'));
    }

    public function createBouquet(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|string', // Validate each item's ID
            'items.*.quantity' => 'required|integer|min:1', // Validate each item's quantity
            'bouquet_image' => 'required|image|max:2048', // Max 2MB
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:15',
            'delivery_date' => 'nullable|date',
            'delivery_address' => 'nullable|string|max:500',
        ]);
    
        $totalPrice = 0;
        $itemsArray = [];
    
        foreach ($request->items as $item) {
            $inventoryItem = Inventory::where('_id', $item['id'])->first();
            if ($inventoryItem && $inventoryItem->quantity >= $item['quantity']) {
                $totalPrice += $inventoryItem->selling_price * $item['quantity'];
                $itemsArray[] = [
                    'item_name' => $inventoryItem->product_name,
                    'quantity' => (int)$item['quantity'],
                ];
    
                // Deduct the quantity from inventory
                $inventoryItem->quantity -= $item['quantity'];
                $inventoryItem->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for ' . $inventoryItem->product_name]);
            }
        }
    
        // Handle bouquet image
        $image = $request->file('bouquet_image');
        $base64Image = base64_encode(file_get_contents($image->getRealPath()));
    
        // Create the bouquet
        Bouquet::create([
            'items' => $itemsArray,
            'total_price' => $totalPrice,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'delivery_date' => $request->delivery_date,
            'delivery_address' => $request->delivery_address,
            'bouquet_image' => 'data:' . $image->getMimeType() . ';base64,' . $base64Image,
        ]);
    
        return response()->json(['success' => true, 'message' => 'Bouquet created successfully']);
    }

    public function fetchBouquetDetails(Request $request)
    {
        $request->validate([
            'bouquet_id' => 'required|string',
        ]);

        $bouquet = Bouquet::where('_id', $request->bouquet_id)->first();

        if (!$bouquet) {
            return response()->json(['success' => false, 'message' => 'Bouquet not found']);
        }

        return response()->json([
            'success' => true,
            'bouquet' => $bouquet,
        ]);
    }

    public function fetchEditModal(Request $request)
    {
        $request->validate([
            'bouquet_id' => 'required|string',
        ]);
    
        $bouquet = Bouquet::where('_id', $request->bouquet_id)->first();
        $inventory = Inventory::all(); // Fetch all inventory items
    
        if (!$bouquet) {
            return response()->json(['success' => false, 'message' => 'Bouquet not found']);
        }
    
        return view('edit-bouquet-modal', compact('bouquet', 'inventory'));
    }

    public function updateBouquet(Request $request)
    {
        $request->validate([
            'bouquet_id' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|string', // Validate each item's ID
            'items.*.quantity' => 'required|integer|min:0', // Validate each item's quantity
            'bouquet_image' => 'nullable|image|max:2048', // Max 2MB
            'edit_customer_name' => 'nullable|string|max:255',
            'edit_customer_email' => 'nullable|email|max:255',
            'edit_customer_phone' => 'nullable|string|max:15',
            'edit_delivery_date' => 'nullable|date',
            'edit_delivery_address' => 'nullable|string|max:500',
        ]);
    
        $bouquet = Bouquet::where('_id', $request->bouquet_id)->first();
    
        if (!$bouquet) {
            return response()->json(['success' => false, 'message' => 'Bouquet not found']);
        }
        // Restore previous items' quantities to inventory
        foreach ($bouquet->items as $item) {
            $inventoryItem = Inventory::where('product_name', $item['item_name'])->first();
            if ($inventoryItem) {
                $inventoryItem->quantity += $item['quantity'];
                $inventoryItem->save();
            }
        }
    
        $totalPrice = 0;
        $itemsArray = [];
        // Update bouquet items and adjust inventory
        foreach ($request->items as $item) {
            $inventoryItem = Inventory::where('_id', $item['id'])->first();
            if ($inventoryItem && $inventoryItem->quantity >= $item['quantity']) {
                $totalPrice += $inventoryItem->selling_price * $item['quantity'];
                $itemsArray[] = [
                    'item_name' => $inventoryItem->product_name,
                    'quantity' => $item['quantity'],
                ];
    
                // Deduct the quantity from inventory
                $inventoryItem->quantity -= $item['quantity'];
                $inventoryItem->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for ' . $inventoryItem->product_name]);
            }
        }
        // Update bouquet image if provided
        if ($request->hasFile('bouquet_image')) {
            $image = $request->file('bouquet_image');
            $base64Image = base64_encode(file_get_contents($image->getRealPath()));
            $bouquet->bouquet_image = 'data:' . $image->getMimeType() . ';base64,' . $base64Image;
        }
    
        // Update bouquet details
        $bouquet->items = $itemsArray;
        $bouquet->total_price = $totalPrice;
        $bouquet->customer_name = $request->edit_customer_name;
        $bouquet->customer_email = $request->edit_customer_email;
        $bouquet->customer_phone = $request->edit_customer_phone;
        $bouquet->delivery_date = $request->edit_delivery_date;
        $bouquet->delivery_address = $request->edit_delivery_address;
        $bouquet->save();
    
        return response()->json(['success' => true, 'message' => 'Bouquet updated successfully']);
    }

    public function getBouquetReceipt(Request $request)
    {
        $request->validate([
            'bouquet_id' => 'required|string',
        ]);
    
        $bouquet = Bouquet::where("_id", $request->bouquet_id)->first();
    
        if (!$bouquet) {
            return response()->json([
                'success' => false,
                'message' => 'Bouquet not found.',
            ]);
        }
    
        // Fetch items from the bouquet
        $items = collect($bouquet->items)->map(function ($item) {
            return [
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items,
            'total_price' => $bouquet->total_price, // Use the total price stored in the bouquet
        ]);
    }

}
