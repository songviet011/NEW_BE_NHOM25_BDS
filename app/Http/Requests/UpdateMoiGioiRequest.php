<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMoiGioiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:moi_giois,id',
            'ten' => 'nullable|string|max:255',
            'so_dien_thoai' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID Môi giới là bắt buộc',
            'id.exists' => 'Môi giới không tồn tại',
            'so_dien_thoai.regex' => 'Số điện thoại phải là 10-11 chữ số',
        ];
    }
}
