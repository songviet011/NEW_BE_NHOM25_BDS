<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGoiTinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_goi'      => 'required|string|max:255|unique:goi_tins,ten_goi',
            'gia'          => 'required|numeric|min:0',
            'so_ngay'      => 'required|integer|min:1',
            'so_luong_tin' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'ten_goi.required'      => 'Tên gói là bắt buộc',
            'ten_goi.unique'        => 'Tên gói đã tồn tại',
            'gia.required'          => 'Giá là bắt buộc',
            'gia.numeric'           => 'Giá phải là số',
            'so_ngay.required'      => 'Số ngày là bắt buộc',
            'so_ngay.integer'       => 'Số ngày phải là số nguyên',
            'so_ngay.min'           => 'Số ngày phải lớn hơn 0',
            'so_luong_tin.required' => 'Số lượng tin là bắt buộc',
            'so_luong_tin.integer'  => 'Số lượng tin phải là số nguyên',
            'so_luong_tin.min'      => 'Số lượng tin phải lớn hơn 0',
        ];
    }
}