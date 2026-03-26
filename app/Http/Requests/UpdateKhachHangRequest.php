<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKhachHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:khach_hangs,id',
            'ten' => 'nullable|string|max:255',
            'so_dien_thoai' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID Khách hàng là bắt buộc',
            'id.exists' => 'Khách hàng không tồn tại',
            'so_dien_thoai.regex' => 'Số điện thoại phải là 10-11 chữ số',
        ];
    }
}
