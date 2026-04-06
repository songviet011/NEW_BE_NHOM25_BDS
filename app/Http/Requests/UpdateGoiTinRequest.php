<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGoiTinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:goi_tins,id',
            'ten_goi' => 'nullable|string|max:255|unique:goi_tins,ten_goi,' . $this->id,
            'gia' => 'nullable|numeric|min:0',
            'so_ngay' => 'nullable|integer|min:1',
            'so_luong_tin' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID gói tin là bắt buộc',
            'id.exists' => 'Gói tin không tồn tại',
            'ten_goi.unique' => 'Tên gói đã tồn tại',
            'gia.numeric' => 'Giá phải là số',
            'so_ngay.integer' => 'Số ngày phải là số nguyên',
            'so_luong_tin.integer' => 'Số lượng tin phải là số nguyên',
        ];
    }
}
