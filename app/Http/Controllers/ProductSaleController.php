<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSale;
use App\Models\SaleNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProductSaleController extends Controller
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

        paginate($request, $limit, $offset);
        $salesQuery = SaleNumber::query();
        $salesQuery->with('sales');
        if ($request->has('saleNo')) {
            $salesQuery->where('saleNo', 'like', filter($request->get('saleNo')));
        }


        $count = $salesQuery->count();
        $sales = $salesQuery->limit($request->get('limit'))->offset($request->get('offset'))->orderBy('id', 'DESC')->get();
        if(count($sales)==0){
            return notFoundError('Sorğuya');
        }
        $sum=0;

        foreach ($sales as $data) {
            $data->saleName='Sale №: '.$data->id;
            $data->productCount=count($data->sales);
            foreach ($data->sales as $sale) {

                $product = Product::where(['id' => $sale->product_id])->first();

                $sale->product_name = $product->name;
                $sale->cost = $product->cost;
                $sale->sum = $sale->quantity * $product->cost;
            }
            $sum += $data->sales->sum('sum');
        }
        $data->total_sum = $sum;


        return response()->json(['data' => $sales, 'total' => $count]);
    }

    public function store(Request $request)
    {
//         $validator = Validator::make($request->all(), [
//             'product_id'=>['required','integer'],
//             'quantity'=>['required','integer'],
//         ]);
//         if ($validator->fails())
//         {
//             return validationError($validator->errors());
//         }


        $createNumber = new SaleNumber;
        $createNumber->sale_no = Str::uuid();
        $createNumber->save();
        $i = 0;
        foreach ($request->product_id as $product_id) {

            $sales = new ProductSale;
            $sales->sale_no = $createNumber->id;
            $sales->product_id = $product_id;
            $sales->quantity = $request->quantity[$i];
            $sales->save();
            $i = $i + 1;
        }
        $id = $createNumber->id;
        $datas = SaleNumber::with('sales')->whereHas('sales', function ($query) use ($id) {
            $query->where('sale_no', '=', $id);
        })
            ->get();
        $sum = 0;
        foreach ($datas as $data) {
            $data->saleName='Sale №: '.$data->id;
            foreach ($data->sales as $sale) {

                $product = Product::where(['id' => $sale->product_id])->first();

                $sale->product_name = $product->name;
                $sale->cost = $product->cost;
                $sale->sum = $sale->quantity * $product->cost;
            }
            $sum += $data->sales->sum('sum');
        }
        $data->total_sum = $sum;

        return createSuccess($datas);

    }

    public function getByNo($no)
    {
        $datas = SaleNumber::with('sales')->where( function ($query) use ($no) {
            $query->where('sale_no', '=', $no);
        })
            ->get();
        if(count($datas)==0){
            return notFoundError($no);
        }

        $sum = 0;
        foreach ($datas as $data) {

            foreach ($data->sales as $sale) {

                $product = Product::where(['id' => $sale->product_id])->first();

                $sale->product_name = $product->name;
                $sale->cost = $product->cost;
                $sale->sum = $sale->quantity * $product->cost;
            }
            $sum += $data->sales->sum('sum');

        }

        $data->total_sum = $sum;

        return response()->json(['data'=>$datas]);

    }
}
