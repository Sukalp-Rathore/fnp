<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function allOrders()
    {
        // Logic to fetch all orders
        return view('orders');
    }
}
