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
            'ten' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'so_dien_thoai' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID Khách hàng là bắt buộc',
            'id.exists' => 'Khách hàng không tồn tại',
            'ten.required' => 'Tên là bắt buộc',
            'ten.string' => 'Tên phải là chuỗi ký tự',
            'ten.max' => 'Tên không được vượt quá 255 ký tự',
            'email.email' => 'Email không hợp lệ',
            'email.max' => 'Email không được vượt quá 255 ký tự',           
            'so_dien_thoai.regex' => 'Số điện thoại phải là 10-11 chữ số',
        ];
    }
}
