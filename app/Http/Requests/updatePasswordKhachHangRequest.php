<?php
// app/Http/Requests/KhachHangUpdatePasswordRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KhachHangUpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => 'required|min:6',
            'password'     => 'required|min:6|confirmed|different:old_password',
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'old_password.min'      => 'Mật khẩu hiện tại phải có ít nhất 6 ký tự.',
            'password.required'     => 'Vui lòng nhập mật khẩu mới.',
            'password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed'    => 'Mật khẩu xác nhận không khớp.',
            'password.different'    => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
        ];
    }
}