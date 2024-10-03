<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class InsertVoucherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "code" => "required|unique:vouchers,code",
            "start_date" => "required|date|date_format:Y-m-d",
            "end_date" => "required|date|date_format:Y-m-d",
            "discount_percent" => "required|numeric|max:100|min:1",
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response(
            ["errors" => $validator->getMessageBag()],

            400));
    }
}
