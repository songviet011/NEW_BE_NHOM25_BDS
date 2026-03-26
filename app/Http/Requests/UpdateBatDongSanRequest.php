<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBatDongSanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:bat_dong_sans,id',
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'gia' => 'nullable|numeric|min:0',
            'dien_tich' => 'nullable|numeric|min:0',
            'loai_id' => 'nullable|integer|exists:loai_bat_dong_sans,id',
            'trang_thai_id' => 'nullable|integer|exists:trang_thai_bat_dong_sans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID BĐS là bắt buộc',
            'id.exists' => 'BĐS không tồn tại',
            'tieu_de.required' => 'Tiêu đề là bắt buộc',
            'loai_id.exists' => 'Loại BĐS không tồn tại',
            'trang_thai_id.exists' => 'Trạng thái không tồn tại',
        ];
    }
}
