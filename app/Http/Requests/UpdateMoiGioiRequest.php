<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMoiGioiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID của user đang đăng nhập để bỏ qua check unique
        $userId = Auth::id(); 

        return [
            'ten'           => "nullable|string|max:255",
            'email'         => "nullable|email|max:255|unique:moi_giois,email," . $userId,
            'so_dien_thoai' => "nullable|string|regex:/^[0-9]{10,11}$/",
            'mo_ta'         => "nullable|string|max:1000",
            'zalo_link'     => "nullable|string|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            'ten.string'            => 'Tên phải là chuỗi ký tự.',
            'ten.max'               => 'Tên không được vượt quá 255 ký tự.',

            'email.email'           => 'Email không đúng định dạng.',
            'email.unique'          => 'Email này đã được sử dụng.',
            
            'so_dien_thoai.regex'   => 'Số điện thoại phải là 10 hoặc 11 chữ số.',
            
            'mo_ta.string'          => 'Mô tả phải là chuỗi ký tự.',
            'mo_ta.max'             => 'Mô tả không được vượt quá 1000 ký tự.',
            
            'zalo_link.string'      => 'Zalo link phải là chuỗi ký tự.',
            'zalo_link.max'         => 'Zalo link không được vượt quá 255 ký tự.',
        ];
    }
}