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
            'purchase_person' => 'required',
            'amount' => 'required',
            'payment_mode' => 'required',
        ]);
        $purchase_person = $request->purchase_person;
        $amount = (int)$request->amount;
        $payment_mode = $request->payment_mode;

        Purchase::create([
            'purchase_person' => $purchase_person,
            'amount' => $amount,
            'payment_mode' => $payment_mode
        ]);
        
        return response()->json(['success' => true, 'message' => 'Entry Successfull']);
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
