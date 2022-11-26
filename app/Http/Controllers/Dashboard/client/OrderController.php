<?php

namespace App\Http\Controllers\Dashboard\client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index()
    {
        //
    } // end of index

    public function create(Client $client)
    {

        $categories = Category::with('products')->get();
        return view('dashboard.clients.orders.create', compact('client', 'categories'));
    } // end of create

    public function store(Request $request, Client $client)
    {

        $request->validate([
            'products' => 'required|array',
            // 'quantities' => 'required|array',
        ]);

        $order = $client->orders()->create([]);

        $order->products()->attach($request->products);

        $total_price = 0;

        foreach ($request->products as $id=>$quantity) {

            $product = Product::findorfail($id);
            $total_price += $product->sale_price * $quantity['quantity'];


            $product->update([
                'stock' => $product->stock - $quantity['quantity']
            ]);

        }

        $order->update([
            'total_price' => $total_price
        ]);

        session()->flash('success',__('site.added_successfully'));
        return redirect()->route('dashboard.orders.index');


    } // end of store 

    public function edit(Order $order, Client $client)
    {
    } // end of edit

    public function update(Request $request, Order $order,Client $client)
    {
        
    } // end of update

    public function destroy(Order $order, Client $client)
    {
        //
    } // end of destroy
} // end of controller
