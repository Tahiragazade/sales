<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getall(Request $request)
    {
        // Str::uuid()
        paginate($request, $limit, $offset);
        $productsQuery = Product::query();

       if($request->has('name')) {
           $productsQuery->where('name', 'like', filter($request->get('name')));
       }
       if($request->has('barcode')) {
        $productsQuery->where('barcode', 'like', filter($request->get('barcode')));
    }

        $count = $productsQuery->count();
        $products = $productsQuery->limit($request->get('limit'))->offset($request->get('offset'))->get();


        return response()->json(['data' => $products, 'total' => $count]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=>['required','string'],
            'barcode'=>['required','string','max:12','min:12'],
            'photo'=>['required'],
            'cost'=>['required'],
            'photo.*' => ['mimes:jpg,jpeg,png,bmp']
        ]);
        if ($validator->fails())
        {
            return validationError($validator->errors());
        }

        $product = new Product;
        $product -> name = $request -> name;
        $product -> barcode=$request->barcode;
        $product -> cost=$request->cost;

        if ($request->hasFile('photo')) {
            $original_filename = $request->file('photo')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = './upload/products/';
            $image = 'product-' . time() . '.' . $file_ext;

            if ($request->file('photo')->move($destination_path, $image)) {
                $product->photo='/upload/products/' . $image;
                $product->save();
                return createSuccess($product);
            } else {
                return validationError('Cannot upload file');
            }
        } else {
            return validationError('File not found');
        }


    }

    public function getByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();
        return response()->json($product);

    }
}
