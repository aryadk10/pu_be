<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidateTransactionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'npwrd' => [
                'required',
                'string',
                Rule::exists('retributors', 'npwrd'),
            ],
            'service_id' => [
                'required',
                'integer',
                Rule::exists('service_retributors', 'id'),
            ],
            'attr' => [
                'required',
                'array',
            ],
            'attr.*.code' => [
                'required',
                'string',
            ],
            'attr.*.values' => [
                'required',
                'array',
            ],
            'attr.*.values.*' => [
                'required',
                'integer',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'npwrd.required' => 'NPWRD harus diisi.',
            'npwrd.exists' => 'NPWRD tidak ditemukan di tabel retributors.',
            'service_id.required' => 'Service ID harus diisi.',
            'service_id.exists' => 'Service ID tidak ditemukan di tabel service_retributors.',
            'amount.min' => 'Jumlah harus lebih dari 0.',
        ];
    }
}
