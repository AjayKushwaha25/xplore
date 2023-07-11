<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidVPA;

class StoreRetailerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'bail|required',
            'mobile_number' => 'bail|required|numeric|digits:10|unique:retailers,mobile_number',
            'whatsapp_number' => 'bail|required|numeric|digits:10',
            'payment_mode' => 'required',
            'upi_id' => ['bail','required','unique:retailers,upi_id', new ValidVPA($this->payment_mode)],
            'uid' => 'sometimes',
            'coupon_code' => 'required|numeric|min:4',
        ];
    }

    public function messages()
    {

        return [
            'upi_id.required' => 'The VPA field is required.',
        ];
    }
}
