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
            'created_by' => 'required|string|max:255',
            'making_charge' => 'nullable|numeric|min:0', // Validate making charge
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
                    'color' => $inventoryItem->color, // Assuming color is a field in Inventory
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
            'total_price' => $totalPrice + (int)$request->making_charge, // Add making charge to total price
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'delivery_date' => setutc($request->delivery_date),
            'delivery_address' => $request->delivery_address,
            'created_by' => $request->created_by,
            'making_charge' => $request->making_charge,
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
        // dd($request->all());
        $request->validate([
            'bouquet_id' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|string', // Validate each item's ID
            'items.*.quantity' => 'required|integer|min:0', // Validate each item's quantity
            'created_by_edit' => 'required|string|max:255',
            'making_charge_edit' => 'nullable|numeric|min:0', // Validate making charge
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
        $oldQuantities = [];
        foreach ($bouquet->items as $item) {
            $oldQuantities[$item['item_name']] = $item['quantity'];
        }

        $totalPrice = 0;
        $itemsArray = [];

        foreach ($request->items as $item) {
            $inventoryItem = Inventory::where('_id', $item['id'])->first();
            if (!$inventoryItem) {
                return response()->json(['success' => false, 'message' => 'Inventory item not found']);
            }

            $oldQty = $oldQuantities[$inventoryItem->product_name] ?? 0;
            $newQty = $item['quantity'];
            $diff = $newQty - $oldQty;

            // If increasing, check for stock
            if ($diff > 0 && $inventoryItem->quantity < $diff) {
                return response()->json(['success' => false, 'message' => 'Insufficient stock for ' . $inventoryItem->product_name]);
            }

            // Update inventory
            $inventoryItem->quantity -= $diff; // This works for both increase and decrease
            $inventoryItem->save();

            $totalPrice += $inventoryItem->selling_price * $newQty;
            $itemsArray[] = [
                'item_name' => $inventoryItem->product_name,
                'color' => $inventoryItem->color ?? null,
                'quantity' => $newQty,
            ];
        }
        // Update bouquet image if provided
        if ($request->hasFile('bouquet_image')) {
            $image = $request->file('bouquet_image');
            $base64Image = base64_encode(file_get_contents($image->getRealPath()));
            $bouquet->bouquet_image = 'data:' . $image->getMimeType() . ';base64,' . $base64Image;
        }
    
        // Update bouquet details
        $bouquet->items = $itemsArray;
        $bouquet->total_price = $totalPrice + (int)$request->making_charge_edit; // Add making charge to total price
        $bouquet->created_by = $request->created_by_edit;
        $bouquet->making_charge = $request->making_charge_edit ?? 0; // Use provided making charge or default to 0
        $bouquet->customer_name = $request->edit_customer_name;
        $bouquet->customer_email = $request->edit_customer_email;
        $bouquet->customer_phone = $request->edit_customer_phone;
        $bouquet->delivery_date = setutc($request->edit_delivery_date);
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
                'color' => $item['color'] ?? 'N/A', // Handle color if it exists
                'quantity' => $item['quantity'],
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items,
            'bouquet_image' => $bouquet->bouquet_image, // Use the bouquet image stored in the bouquet
            'total_price' => $bouquet->total_price, // Use the total price stored in the bouquet
        ]);
    }

}
