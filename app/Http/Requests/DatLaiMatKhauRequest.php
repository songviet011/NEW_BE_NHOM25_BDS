<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatLaiMatKhauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'code'     => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'        => 'Email không được để trống',
            'email.email'           => 'Email không hợp lệ',
            'code.required'         => 'Mã xác nhận không được để trống',
            'code.digits'           => 'Mã xác nhận phải gồm 6 chữ số',
            'password.required'     => 'Mật khẩu mới không được để trống',
            'password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'password.confirmed'    => 'Xác nhận mật khẩu mới không khớp',
        ];
    }
}