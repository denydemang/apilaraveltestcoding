<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyVoucherRequest;
use App\Http\Requests\InsertVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VoucherResource;
use App\Http\Resources\VoucherResourcesCollection;
use App\Models\Product;
use App\Models\Voucher;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    private int $statusCode;

    public function __construct()
    {
        $this->statusCode = 500;
    }

    public function get(Request $request, Voucher $voucher) : VoucherResourcesCollection{

        try {


            $data = $voucher->all();

            return new VoucherResourcesCollection($data, 'Successfully Get Vouchers');


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

    public function getdetail($id ,Voucher $voucher) : VoucherResource

    {
        try {

            $data = $voucher->where("id", $id)->first();

            if ($data == null){
                $this->statusCode =404;
                throw new Exception("Voucher Not Found!");
            }
            return new VoucherResource($data, 'Successfully Get Detail Voucher');

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


    public function create(InsertVoucherRequest $request, Voucher $voucher) : VoucherResource{

        try {

            $dataValidated = $request->validated();

            $voucher->code = $dataValidated['code'];
            $voucher->start_date = $dataValidated['start_date'];
            $voucher->end_date = $dataValidated['end_date'];
            $voucher->discount = $dataValidated['discount_percent'];

            $voucher->save();

            return new VoucherResource($voucher, "Successfully Created New Voucher");

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

    public function update($id ,UpdateVoucherRequest $request, Voucher $voucher) : VoucherResource{
        try {

            $dataValidated = $request->validated();

            $dataUpdate = $voucher->where("id", $id)->first();

            if ($dataUpdate == null){

                $this->statusCode =404;
                throw new Exception("Cannot Update ,Voucher With ID $id Not Found");

            }

            $dataUpdate->code = $dataValidated['code'];
            $dataUpdate->start_date = $dataValidated['start_date'];
            $dataUpdate->end_date = $dataValidated['end_date'];
            $dataUpdate->discount = $dataValidated['discount_percent'];

            $dataUpdate->save();

            return new VoucherResource($dataUpdate, "Successfully Updated Voucher $id");

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

    public function delete($id, Voucher $voucher) : VoucherResource{
        try {


            $dataDelete = $voucher->where("id", $id)->first();

            if ($dataDelete == null){
                $this->statusCode =404;
                throw new Exception("Cannot Delete ,Voucher With ID $id Not Found");

            }
            $dataDelete->delete();

            return new VoucherResource($dataDelete, "Successfully Deleted Voucher $id");

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

    public function apply(ApplyVoucherRequest $request, Voucher $voucher, Product $product) : ProductResource {


        try {
            $dataValidated = $request->validated();

            $dataVoucher = $voucher->where("code", $dataValidated['vouchercode'])->first();
            if ( $this->validVoucher($dataVoucher->code)){


                $percentDiscount = intval($dataVoucher->discount);

                $productData =  $product->where("id", $dataValidated['productid'])->first();

                $productData->price -= intval($productData->price) * ($percentDiscount /100);
                $productData->update();
            }

            return new ProductResource($productData, "Successfully Applied Voucher $dataVoucher->code To Product $productData->name With Percentage $dataVoucher->discount %");

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

    public function validVoucher($code) : bool{

         // Ambil data voucher berdasarkan kode
    $dataVoucher = Voucher::where("code", $code)->first();

    // Periksa apakah voucher ditemukan
    if (!$dataVoucher) {
        throw new Exception("Voucher not found!");
    }

    // Dapatkan waktu saat ini dalam bentuk date
    $now = Carbon::now()->toDateString();

    // Konversi tanggal mulai dan tanggal akhir voucher menjadi string date (Y-m-d)
    $startDate = Carbon::parse($dataVoucher->start_date)->toDateString();
    $endDate = Carbon::parse($dataVoucher->end_date)->toDateString();

    // Bandingkan tanggal
    if ($now >= $startDate && $now <= $endDate) {
        return true; // Voucher valid
    }

    // Jika voucher belum aktif
    if ($now < $startDate) {
        $this->statusCode = 402;
        throw new Exception("Voucher is Not Active Yet!");
    }

    // Jika voucher sudah kedaluwarsa
    if ($now > $endDate) {
        $this->statusCode = 402;
        throw new Exception("Voucher Already Expired!");
    }

    return false; // Tidak valid jika tidak memenuhi syarat



    }
}
