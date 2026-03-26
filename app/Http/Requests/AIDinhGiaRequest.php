<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AIDinhGiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loai_id' => 'required|integer|exists:loai_bat_dong_sans,id',
            'dien_tich' => 'required|numeric|min:0',
            'tinh_id' => 'required|integer|exists:tinh_thanhs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'loai_id.required' => 'Vui lòng nhập loại BĐS',
            'loai_id.exists' => 'Loại BĐS không tồn tại',
            'dien_tich.required' => 'Vui lòng nhập diện tích',
            'dien_tich.numeric' => 'Diện tích phải là số',
            'tinh_id.required' => 'Vui lòng nhập tỉnh',
            'tinh_id.exists' => 'Tỉnh không tồn tại',
        ];
    }
}
