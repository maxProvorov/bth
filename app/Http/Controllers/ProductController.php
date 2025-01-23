<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Jobs\SendProductNotification;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::available()->orderBy('id')->get();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $data = array_combine($request->input('data_keys', []), $request->input('data_values', []));
        
        $validated = $request->validate([
            'name' => 'required|string|min:10',
            'article' => 'required|string|alpha_num',
            'status' => 'required',
        ]);

        $product = Product::create(array_merge($validated, ['data' => $data]));

        SendProductNotification::dispatch($product);
        
        return redirect()->route('products.index');
    }

    public function update(Request $request, Product $product)
    {
        $data = array_combine($request->input('data_keys', []), $request->input('data_values', []));

        $validated = $request->validate([
            'name' => 'required|string|min:10',
            'article' => 'required|string|alpha_num',
            'status' => 'required',
        ]);

        $product->update(array_merge($validated, ['data' => $data]));

        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
}