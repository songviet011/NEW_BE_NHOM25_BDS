<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMoiGioiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'            => 'required|integer|exists:moi_giois,id',
            'ten'           => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'so_dien_thoai' => 'nullable|string|regex:/^[0-9]{10,11}$/',
            'mo_ta'         => 'nullable|string|max:255',
            'zalo_link'     => 'nullable|string|max:255',
            'is_active'     => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'           => 'ID Môi giới là bắt buộc',
            'id.exists'             => 'Môi giới không tồn tại',
            'so_dien_thoai.regex'   => 'Số điện thoại phải là 10-11 chữ số',
            'email.email'           => 'Email không hợp lệ',
            'email.max'             => 'Email không được vượt quá 255 ký tự',
            'ten.string'           => 'Tên phải là chuỗi ký tự',
            'ten.max'              => 'Tên không được vượt quá 255 ký tự',
            'mo_ta.string'         => 'Mô tả phải là chuỗi ký tự',
            'mo_ta.max'            => 'Mô tả không được vượt quá 255 ký tự',
            'zalo_link.string'     => 'Zalo link phải là chuỗi ký tự',
            'zalo_link.max'        => 'Zalo link không được vượt quá 255 ký tự',
            'is_active.boolean'    => 'Trạng thái phải là giá trị boolean',
        ];  
    }
}
