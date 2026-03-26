<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeBatDongSanStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:bat_dong_sans,id',
            'trang_thai_id' => 'required|integer|exists:trang_thai_bat_dong_sans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID BĐS là bắt buộc',
            'id.exists' => 'BĐS không tồn tại',
            'trang_thai_id.required' => 'Trạng thái là bắt buộc',
            'trang_thai_id.exists' => 'Trạng thái không tồn tại',
        ];
    }
}
