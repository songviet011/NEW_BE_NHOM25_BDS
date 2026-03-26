<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBatDongSanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tieu_de' => 'required|string|max:255',
            'gia' => 'required|numeric|min:0',
            'dien_tich' => 'required|numeric|min:0',
            'loai_id' => 'required|integer|exists:loai_bat_dong_sans,id',
            'trang_thai_id' => 'required|integer|exists:trang_thai_bat_dong_sans,id',
            'mo_ta' => 'nullable|string',
            'tinh_id' => 'nullable|integer|exists:tinh_thanhs,id',
            'quan_id' => 'nullable|integer|exists:quan_huyens,id',
            'dia_chi_id' => 'nullable|integer|exists:dia_chis,id',
            'so_phong_ngu' => 'nullable|integer|min:0',
            'so_phong_tam' => 'nullable|integer|min:0',
            'is_noi_bat' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'tieu_de.required' => 'Tiêu đề là bắt buộc',
            'gia.required' => 'Giá là bắt buộc',
            'gia.numeric' => 'Giá phải là số',
            'dien_tich.required' => 'Diện tích là bắt buộc',
            'dien_tich.numeric' => 'Diện tích phải là số',
            'loai_id.required' => 'Loại BĐS là bắt buộc',
            'loai_id.exists' => 'Loại BĐS không tồn tại',
            'trang_thai_id.required' => 'Trạng thái là bắt buộc',
            'trang_thai_id.exists' => 'Trạng thái không tồn tại',
            'tinh_id.exists' => 'Tỉnh không tồn tại',
            'quan_id.exists' => 'Quận không tồn tại',
            'dia_chi_id.exists' => 'Địa chỉ không tồn tại',
        ];
    }
}
