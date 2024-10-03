<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsertProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductResourceCollection;
use App\Http\Resources\ProductResourcesCollection;
use App\Models\Product;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private int $perpage ;
    private int $page ;
    private int $statusCode;

    public function __construct()
    {
        $this->statusCode = 500;
    }

    public function get(Request $request, Product $product) : ProductResourcesCollection{

        try {

            $this->perpage = $request->has('perPage') ? intval($request->perPage) : 10;
            $this->page = $request->has('page') ? intval($request->page) : 1;

            $data = Product::paginate(perPage: $this->perpage, page:$this->page);

            return new ProductResourcesCollection($data, 'Successfully Get Products');


        } catch (\Throwable $th) {

            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],$this->statusCode));
        }
    }

    public function getdetail($id ,Product $product) : ProductResource

    {
        try {

            $data = $product->where("id", $id)->first();

            if ($data == null){
                $this->statusCode = 404;
                throw new Exception("Product Not Found!");
            }

            return new ProductResource($data, 'Successfully Get Detail Product');

        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],$this->statusCode));
        }

    }

    public function search(Request $request , Product $products) : ProductResourcesCollection{


        try {
            $this->perpage = $request->has('perPage') ? intval($request->perPage) : 10;
            $this->page = $request->has('page') ? intval($request->page) : 1;

            $key = $request->has('key') ?  $request->key  : "" ;


            $data = $products->where(function($query) use($key){

                $query->where("name" , "like", "%$key%");
                $query->orWhere("category" , "like", "%$key%");
                $query->orWhere("description" , "like", "%$key %");
                $query->orWhere("price" , "like", "%$key%");
            })
            ->paginate(perPage:$this->perpage ,page:$this->page);

            $msg =  $key ? " With Key " .$key : '';
            return new ProductResourcesCollection($data, "Successfully Search Product$msg");
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],$this->statusCode));

        }

    }

    public function create(InsertProductRequest $request, Product $product) : ProductResource{

        try {

            $dataValidated = $request->validated();

            $product->name = $dataValidated['name'];
            $product->category = $dataValidated['category'];
            $product->description = $dataValidated['description'];
            $product->price = $dataValidated['price'];

            $product->save();

            return new ProductResource($product, "Successfully Created New Product");

        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],$this->statusCode));
        }
    }

    public function update($id ,UpdateProductRequest $request, Product $product) : ProductResource{
        try {

            $dataValidated = $request->validated();

            $dataUpdate = $product->where("id", $id)->first();
            if ($dataUpdate == null){

                $this->statusCode =404;
                throw new Exception("Cannot Update ,Product With ID $id Not Found");

            }
            $dataUpdate->name = $dataValidated['name'];
            $dataUpdate->category = $dataValidated['category'];
            $dataUpdate->description = $dataValidated['description'];
            $dataUpdate->price = $dataValidated['price'];

            $dataUpdate->update();

            return new ProductResource($dataUpdate, "Successfully Updated Product $id");

        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],$this->statusCode));
        }
    }

    public function delete($id, Product $product) : ProductResource{
        try {


            $dataDelete = $product->where("id", $id)->first();

            if ($dataDelete == null){
                $this->statusCode =404;
                throw new Exception("Cannot Delete ,Product With ID $id Not Found");

            }
            $dataDelete->delete();

            return new ProductResource($dataDelete, "Successfully Deleted Product $id");

        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],$this->statusCode));
        }
    }
}
