<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
 // GET /products
 public function index()
 {
     $products = DB::select('SELECT * FROM products');

     return view('products.index', compact('products'));

    //  return response()->json($products);

    }
     // POST /products
     public function store(Request $request)
     {
        //  dd('test');
         $request->validate([
             'name' => 'required|string',
             'base_price' => 'required|numeric|min:0',
             'category' => 'required|in:cold,hot',
         ]);
         DB::insert('
             INSERT INTO products (name, base_price, category, created_at, updated_at)
             VALUES (?, ?, ?, datetime("now"), datetime("now"))
         ', [
             $request->name,
             $request->base_price,
             $request->category,
         ]);

         return response()->json(['message' => 'Product created'], 201);
     }
     public function show($id)
     {
         $product = DB::select('SELECT * FROM products WHERE id = ?', [$id]);
         return response()->json($product);
     }

     public function edit($id)
     {
         $product = DB::table('products')->where('id', $id)->first();

         if (!$product) {
             return redirect()->back()->with('error', 'product not found.');
         }

         return response()->json(['message'=>'Product is editted successfully'],201);
     }

     public function update(Request $request, $id)
     {
         $product = DB::table('products')->where('id', $id)->first();

         if (!$product) {
             return response()->json(['error' => 'Product not found.'], 404);
         }

         $request->validate([
             'name' => 'required|string',
             'base_price' => 'required|numeric|min:0',
         ]);

         $updated = DB::table('products')->where('id', $id)->update([
             'name' => $request->input('name'),
             'base_price' => $request->input('base_price'),
             'updated_at' => now()
         ]);

         if ($updated) {
             return response()->json(['message' => 'Product updated successfully.'], 200);
         } else {
             return response()->json(['message' => 'No changes made.'], 200);
         }
     }

     public function destroy($id)
     {
         DB::delete('DELETE FROM products WHERE id = ?', [$id]);

         return response()->json(['message' => 'Product deleted']);
     }
 }
