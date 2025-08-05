<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;

class SalesController extends Controller
{
    // 
    public function sales()
    {
        $sales = Sale::get();
        return view('sales', compact('sales'));
    }

    public function enterSales(Request $request)
    {
        $request->validate([
            'cashsales' => 'required',
            'creditsales' => 'required',
            'onlinesales' => 'required',
        ]);
        $cash_sales = (int)$request->cashsales;
        $credit_sales = (int)$request->creditsales;
        $online_sales = (int)$request->onlinesales;
        $total_sale = $cash_sales + $credit_sales + $online_sales;
        $currentMonth = now()->month;

        Sale::create([
            'cash_sale' => $cash_sales,
            'online_sale' => $online_sales,
            'credit_sale' => $credit_sales,
            'total_sale' => $total_sale,
            'month' => $currentMonth,
            'date' => $request->date
        ]);
        
        return response()->json(['success' => true, 'message' => 'Entry Successfull']);
    }

    public function deleteSale(Request $request)
    {
        $saleId = $request->input('saleId');
        Sale::destroy($saleId);
        return response()->json(['success' => true, 'message' => 'Sale deleted successfully']);
    }
}
