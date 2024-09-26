<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUpdateCarRequest extends FormRequest
{
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
        $carId = $this->route('car');

        return [
            'brand' => 'required|string|min:2|max:50',
            'model' => 'required|string|min:2|max:50',
            'plate_number' => 'required|string',
            'price_rate' => 'required|numeric|min:0',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'code' => 400,
            'message' => 'Validation Errors',
            'errors' => $validator->errors()
        ]));
    }
}
