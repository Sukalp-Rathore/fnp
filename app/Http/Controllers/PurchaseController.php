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

    // AJAX endpoint to fetch purchases for a vendor
    public function getVendorPurchases(Request $request)
    {
        $request->validate([
            'vendor_name' => 'required|string',
        ]);
        $purchases = Purchase::where('purchase_person', $request->vendor_name)->get();
        // Return a partial Blade view or JSON
        return response()->json([
            'success' => true,
            'purchases' => $purchases,
        ]);
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
        // Update vendor summary
        $vendor = PurchaseVendor::where('name', $purchasePerson)->first();

        if ($vendor) {
            $vendor->total_purchase += $amount;
            $vendor->amount_pending += ($amount - $paid);
            $vendor->save();
        }

        // Save the purchase
        Purchase::create([
            'purchase_person' => $purchasePerson,
            'amount' => $amount,
            'payment_mode' => $request->payment_mode,
            'paid_amount' => $paid,
            'total_pending_amount' => $vendor->amount_pending,
            'payment_status' => $status,
            'date' => $request->date,
        ]);
    
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

    public function showEditPurchase(Request $request)
    {
        $request->validate([
            'purchaseId' => 'required',
        ]);
        $purchaseId = $request->input('purchaseId');
        $purchase = Purchase::where('_id', $purchaseId)->first();
        $vendors = PurchaseVendor::get();

        return view('edit-purchase', compact('purchase', 'vendors'));
    }

    public function updatePurchase(Request $request)
    {
        $request->validate([
            'purchaseId' => 'required|exists:purchases,_id',
            'purchase_person' => 'required|string',
            'amount' => 'required|numeric',
            'payment_mode' => 'required|string',
            'paid_amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $purchase = Purchase::find($request->purchaseId);
        if (!$purchase) {
            return response()->json(['success' => false, 'message' => 'Purchase entry not found.'], 404);
        }

        // Reverse old vendor calculations
        $oldVendor = PurchaseVendor::where('name', $purchase->purchase_person)->first();
        if ($oldVendor) {
            $oldVendor->total_purchase -= $purchase->amount;
            $oldVendor->amount_pending -= ($purchase->amount - $purchase->paid_amount);
            $oldVendor->save();
        }

        // Calculate new status
        $amount = (float) $request->amount;
        $paid = (float) $request->paid_amount;
        $status = 'pending';
        if ($paid >= $amount) {
            $status = 'paid';
        } elseif ($paid > 0 && $paid < $amount) {
            $status = 'part-payment';
        }

        // Update new vendor calculations
        $newVendor = PurchaseVendor::where('name', $request->purchase_person)->first();
        if ($newVendor) {
            $newVendor->total_purchase += $amount;
            $newVendor->amount_pending += ($amount - $paid);
            $newVendor->save();
        }

        // Update purchase entry
        $purchase->purchase_person = $request->purchase_person;
        $purchase->amount = $amount;
        $purchase->payment_mode = $request->payment_mode;
        $purchase->paid_amount = $paid;
        $purchase->total_pending_amount = $newVendor ? $newVendor->amount_pending : 0;
        $purchase->payment_status = $status;
        $purchase->date = $request->date;
        $purchase->save();

        return response()->json(['success' => true, 'message' => 'Purchase entry updated successfully.']);
    }   

    public function deletePurchase(Request $request)
    {
        $request->validate([
            'purchaseId' => 'required|exists:purchases,_id',
        ]);

        $purchase = Purchase::find($request->purchaseId);
        if (!$purchase) {
            return response()->json(['success' => false, 'message' => 'Purchase entry not found.'], 404);
        }

        // Reverse vendor calculations
        $vendor = PurchaseVendor::where('name', $purchase->purchase_person)->first();
        if ($vendor) {
            $vendor->total_purchase -= $purchase->amount;
            $vendor->amount_pending -= ($purchase->amount - $purchase->paid_amount);
            $vendor->save();
        }

        $purchase->delete();

        return response()->json(['success' => true, 'message' => 'Purchase entry deleted successfully.']);
    }

}
