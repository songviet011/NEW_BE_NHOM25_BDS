<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBatDongSanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:bat_dong_sans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Vui lòng cung cấp ID bất động sản.',
            'id.integer' => 'ID bất động sản không hợp lệ.',
            'id.exists' => 'Bất động sản không tồn tại.',
        ];
    }
}
