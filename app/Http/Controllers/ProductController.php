<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        if ($products->count() > 0) {
            return response()->json(['status' => 200, 'product' => $products], 200);
        } else {
            return response()->json(['status' => 404, 'message' => 'No Record Found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|string',
            'description' => 'required|string',
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->messages()], 422);
        } else {
            $productData = [
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->move(public_path('Product_image'),$imageName);

                $productData['image'] = $imagePath;
            } else {
                return response()->json(['status' => 422, 'message' => 'Image upload failed'], 422);
            }

            $product = Product::create($productData);

            if ($product) {
                return response()->json(['status' => 200, 'message' => 'Product Created Successfully'], 200);
            } else {
                return response()->json(['status' => 500, 'message' => 'Something Went Wrong!'], 500);
            }
        }
    }

    public function show($id){
        $product = Product::find($id);
        if($product){
            return response()->json(['status' => 200, 'product' => $product], 200);
        } else {
            return response()->json(['status' => 404, 'message' => 'No Record Found'], 404);
        }

    }

    public function edit($id){
        $product = Product::find($id);
        if($product){
            return response()->json(['status' => 200, 'product' => $product], 200);
        } else {
            return response()->json(['status' => 404, 'message' => 'No Record Found'], 404);
        }
    }

    public function update(Request $request, int $id){
   
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'price' => 'required|string',
        'description' => 'required|string',
        'image' => 'nullable'
    ]);

   
    if ($validator->fails()) {
        return response()->json(['status' => 422, 'errors' => $validator->messages()], 422);
    }

    $product = Product::find($id);
    if (!$product) {
        return response()->json(['status' => 404, 'message' => 'Product not found'], 404);
    }

    $productData = [
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
    ];

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('Product_image'), $imageName);

        $productData['image'] = 'Product_image/' . $imageName;
    }

    $product->update($productData);

    return response()->json(['status' => 200, 'message' => 'Product Updated Successfully'], 200);
}

public function delete($id){

    $product = Product::find($id);

    if($product){

       $product->delete();
       return response()->json(['status' => 200, 'message' => 'Product Deleted Successfully'], 200);
    } else {
        return response()->json(['status' => 404, 'message' => 'product id Not Found'], 404);
    }

}

}
