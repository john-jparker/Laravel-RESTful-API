<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ProductNotBelongsToUser;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProductCollection::collection(Product::paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try{
            DB::beginTransaction();
            $product = Product::create([
                'product_name'=>$request->product_name,
                'details'=>$request->details,
                'price'=>$request->price,
                'stock'=>$request->stock,
                'discount'=>$request->discount,
                'status'=>$request->status,
            ]);

            if(!empty($product)){
                DB::commit();

                return response([
                    'data'=>new ProductResource($product)
                ],Response::HTTP_CREATED);
            }else{
                throw new \Exception('Invalid Information', 400);
            }
        }catch (\Exception $ex){
            DB::rollBack();
            return response([
                'data'=>$ex->getMessage(),
            ],Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {

        return new ProductResource($product);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $this->productUserCheck($product);
        try{
            DB::beginTransaction();
            $product->update([
                'product_name'=>$request->product_name,
                'details'=>$request->details,
                'price'=>$request->price,
                'stock'=>$request->stock,
                'discount'=>$request->discount,
                'status'=>$request->status,
            ]);

            if(!empty($product)){
                DB::commit();
                return response([
                    'data'=>new ProductResource($product)
                ],Response::HTTP_OK);
            }else{
                throw new \Exception('Invalid Information', 400);
            }
        }catch (\Exception $ex){
            DB::rollBack();
            return response([
                'data'=>$ex->getMessage(),
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $this->productUserCheck($product);

        if($product->delete()){

            Review::where('product_id', $product->id)->delete();

            return response([
                'data'=>'Product Deleted'
            ],Response::HTTP_NO_CONTENT);

        }else{
            return response([
                'data'=>'Not Deleted',
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    public function productUserCheck($product){
        if(auth()->id() !== $product->user_id)
            throw new ProductNotBelongsToUser;
    }
}
