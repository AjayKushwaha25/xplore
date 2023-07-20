<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidVPA implements Rule
{

    protected $paymentMode;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($paymentMode)
    {
        $this->paymentMode = $paymentMode;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // UPI ID validation logic
        $isUpiId = preg_match('/^([a-zA-Z0-9]+)([\.{1}])?([a-zA-Z0-9]+)\@?(okicici|oksbi|okaxis|okhdfcbank|ybl|upi|axl)+$/', $value);

        // Phone number validation logic
        /*$isPhoneNumber = preg_match('/^[6-9]\d{9}$/', $value);

        return $isUpiId || $isPhoneNumber;*/

        return $isUpiId
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if($this->paymentMode == 'upi_id'){
            return 'The given VPA must be a valid UPI ID.';
        }

        if($this->paymentMode == 'paytm_number'){
            return 'The given VPA must be a valid Paytm Number.';
        }

        return 'The :attribute must be a valid VPA.';
    }
}
