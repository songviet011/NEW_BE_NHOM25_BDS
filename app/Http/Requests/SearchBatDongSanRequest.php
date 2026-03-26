<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBatDongSanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tinh_id' => 'nullable|integer|exists:tinh_thanhs,id',
            'loai_id' => 'nullable|integer|exists:loai_bat_dong_sans,id',
            'gia_min' => 'nullable|numeric|min:0',
            'gia_max' => 'nullable|numeric|min:0',
            'tieu_de' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'tinh_id.exists' => 'Tỉnh không tồn tại',
            'loai_id.exists' => 'Loại BĐS không tồn tại',
            'gia_min.numeric' => 'Giá tối thiểu phải là số',
            'gia_max.numeric' => 'Giá tối đa phải là số',
            'page.integer' => 'Trang phải là số',
        ];
    }
}
