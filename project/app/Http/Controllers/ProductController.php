<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\Product;

class ProductController extends Controller
{
 
    public function __construct() {
        $this->middleware('auth:api');
    }

    
    public function index()
    {
        $products = Product::all();
        
        return response()->json([
            "status" => true,
            "message" => "Product List",
            "data" => $products
        ]);
    }

   
    public function store(Request $request)
    {
        $request_data = $request->all();
        
        $validator = Validator::make($request_data, [
            'name' => 'required',
            'mrp' => 'required',
            'price' => 'required',
            'quantity' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ]);
        }

        $product = Product::create($request_data);
        
        return response()->json([
            "status" => true,
            "message" => "Product created successfully.",
            "data" => $product
        ]);
    }

    
    public function show(Product $product)
    {
        if (is_null($product)) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Product found.",
            "data" => $product
        ]);
    }


    public function update(Request $request, Product $product)
    {
        $request_data = $request->all();
        
        $validator = Validator::make($request_data, [
            'name' => 'required',
            'mrp' => 'required',
            'price' => 'required',
            'quantity' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ]);      
        }

        $product->name = $request_data['name'];
        $product->mrp = $request_data['mrp'];
        $product->price = $request_data['price'];
        $product->quantity = $request_data['quantity'];
        $product->save();
        
        return response()->json([
            "status" => true,
            "message" => "Product updated successfully.",
            "data" => $product
        ]);
    }

    
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            "status" => true,
            "message" => "Product deleted successfully.",
            "data" => $product
        ]);
    }
}
