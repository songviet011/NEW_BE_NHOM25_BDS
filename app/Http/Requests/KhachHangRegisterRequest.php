<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KhachHangRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten'                 => 'required|string|max:255',
            'email'               => 'required|email|unique:khach_hangs,email|max:255',
            'so_dien_thoai'       => 'required|string|regex:/^[0-9]{10,11}$/|unique:khach_hangs,so_dien_thoai',
            'password'            => 'required|string|min:6|confirmed',
            'zalo_link'           => 'nullable|string|max:255',
            'mo_ta'               => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'ten.required'            => 'Tên là bắt buộc',
            'ten.string'              => 'Tên phải là chuỗi ký tự',
            'ten.max'                 => 'Tên không được vượt quá 255 ký tự',
            
            'email.required'          => 'Email là bắt buộc',
            'email.email'             => 'Email phải là định dạng email hợp lệ',
            'email.unique'            => 'Email đã được sử dụng',
            'email.max'               => 'Email không được vượt quá 255 ký tự',
            
            'so_dien_thoai.required'  => 'Số điện thoại là bắt buộc',
            'so_dien_thoai.regex'     => 'Số điện thoại phải là 10-11 chữ số',
            'so_dien_thoai.unique'    => 'Số điện thoại đã được đăng ký',
            
            'password.required'       => 'Mật khẩu là bắt buộc',
            'password.min'            => 'Mật khẩu phải ít nhất 6 ký tự',
            'password.confirmed'      => 'Mật khẩu xác nhận không khớp',
            
            'zalo_link.string'        => 'Zalo link phải là chuỗi ký tự',
            'mo_ta.string'            => 'Mô tả phải là chuỗi ký tự',
        ];
    }
}