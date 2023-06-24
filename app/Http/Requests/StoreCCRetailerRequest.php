<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCCRetailerRequest extends FormRequest
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
            'mobile_number' => 'bail|required|numeric|digits:10',
            'whatsapp_number' => 'bail|required|numeric|digits:10',
            'upi_id' => 'bail|required',
        ];
    }
}
