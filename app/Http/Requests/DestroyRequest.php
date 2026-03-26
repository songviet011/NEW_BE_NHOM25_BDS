<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID là bắt buộc',
            'id.integer' => 'ID phải là số',
        ];
    }
}
