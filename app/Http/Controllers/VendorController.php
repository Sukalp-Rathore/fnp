<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    //
    public function vendors()
    {
        $vendors = Vendor::get();
        return view('vendors' , compact('vendors'));
    }

    public function createVendor(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'city' => 'required',
            'gender' => 'required',
        ]);

        Vendor::create([
            'first_name' => $request->first_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'city' => $request->city,
            'gender' => $request->gender,
        ]);
        return response()->json(['success' => true, 'message' => 'Vendor created successfully']);
    }

    public function showEditVendor(Request $request)
    {   
        $request->validate([
            'vendorId' => 'required',
        ]);
        $vendorId = $request->input('vendorId');
        $vendor = Vendor::where('_id', $vendorId)->first();

        return view('edit-vendor', compact('vendor'));
    }

    public function updateVendor(Request $request)
    {
        $request->validate([
            'vendorId' => 'required',
            'first_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'city' => 'required',
            'gender' => 'required',
        ]);
        $vendorId = $request->input('vendorId');
        $vendor = Vendor::where('_id', $vendorId)->first();
        $vendor->first_name = $request->input('first_name');
        $vendor->email = $request->input('email');
        $vendor->mobile = $request->input('mobile');
        $vendor->city = $request->input('city');
        $vendor->gender = $request->input('gender');
        $vendor->save();

        return response()->json(['success' => true, 'message' => 'Vendor updated successfully']);
    }

    public function deleteVendor(Request $request)
    {
        $request->validate([
            'vendorId' => 'required',
        ]);
        $vendorId = $request->input('vendorId');
        Vendor::where('_id', $vendorId)->delete();
        return response()->json(['success' => true, 'message' => 'Vendor deleted successfully']);
    }
}
