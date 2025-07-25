<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseVendor;

class PurchaseController extends Controller
{
    //
    public function purchase()
    {
        $purchases = Purchase::get();
        $vendors = PurchaseVendor::get();
        return view('purchase', compact('purchases','vendors'));
    }

    public function enterPurchase(Request $request)
    {
        $request->validate([
            'purchase_person' => 'required|string',
            'amount' => 'required|numeric',
            'payment_mode' => 'required|string',
            'paid_amount' => 'required|numeric',
        ]);
    
        $purchasePerson = $request->purchase_person;
        $amount = (float) $request->amount;
        $paid = (float) $request->paid_amount;
        $status = 'pending';
    
        if ($paid >= $amount) {
            $status = 'paid';
        } elseif ($paid > 0 && $paid < $amount) {
            $status = 'part-payment';
        }
    
        // Save the purchase
        Purchase::create([
            'purchase_person' => $purchasePerson,
            'amount' => $amount,
            'payment_mode' => $request->payment_mode,
            'paid_amount' => $paid,
            'payment_status' => $status
        ]);
    
        // Update vendor summary
        $vendor = PurchaseVendor::where('name', $purchasePerson)->first();
        if ($vendor) {
            $vendor->total_purchase += $amount;
            $vendor->amount_pending += ($amount - $paid);
            $vendor->save();
        }
    
        return response()->json(['success' => true, 'message' => 'Entry Successful']);
    }

    public function index()
    {
        $vendors = PurchaseVendor::all();
        return response()->json(['success' => true, 'vendors' => $vendors]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PurchaseVendor::create(['name' => $request->name]);

        return response()->json(['success' => true, 'message' => 'Vendor added successfully']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
        ]);
        $vendor = PurchaseVendor::find($request->id);
        $vendor->name = $request->name;
        $vendor->save();

        return response()->json(['success' => true, 'message' => 'Vendor updated successfully']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        PurchaseVendor::find($request->id)->delete();

        return response()->json(['success' => true, 'message' => 'Vendor deleted successfully']);
    }
}
