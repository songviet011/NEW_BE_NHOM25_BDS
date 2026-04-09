<?php
// app/Http/Requests/KhachHangUpdateProfileRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class KhachHangUpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'ten'                 => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255|unique:khach_hangs,email,' . $userId,
            'so_dien_thoai'       => 'nullable|string|regex:/^[0-9]{10,11}$/|unique:khach_hangs,so_dien_thoai,' . $userId,
        ];
    }

    public function messages(): array
    {
        return [
            'ten.string'              => 'Tên phải là chuỗi ký tự.',
            'ten.max'                 => 'Tên không được vượt quá 255 ký tự.',
            'email.email'             => 'Email không đúng định dạng.',
            'email.unique'            => 'Email này đã được sử dụng.',
            'so_dien_thoai.regex'     => 'Số điện thoại phải là 10-11 chữ số.',
            'so_dien_thoai.unique'    => 'Số điện thoại đã được đăng ký.',
        ];
    }
}