<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateImageBatDongSanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'anh_id' => 'required|integer|exists:hinh_anh_bat_dong_sans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'anh_id.required' => 'Vui lòng chọn ảnh cần đặt làm ảnh đại diện.',
            'anh_id.integer'  => 'ID ảnh không hợp lệ.',
            'anh_id.exists'   => 'Ảnh không tồn tại trong hệ thống.',
        ];
    }
}
