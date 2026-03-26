<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoiGioiRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten' => 'required|string|max:255',
            'email' => 'required|email|unique:moi_giois,email',
            'so_dien_thoai' => 'required|string|regex:/^[0-9]{10,11}$/',
            'password' => 'required|string|min:6|confirmed',
            'zalo_link' => 'nullable|url',
            'mo_ta' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'ten.required' => 'Tên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email phải là định dạng email hợp lệ',
            'email.unique' => 'Email đã được sử dụng',
            'so_dien_thoai.required' => 'Số điện thoại là bắt buộc',
            'so_dien_thoai.regex' => 'Số điện thoại phải là 10-11 chữ số',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
        ];
    }
}
