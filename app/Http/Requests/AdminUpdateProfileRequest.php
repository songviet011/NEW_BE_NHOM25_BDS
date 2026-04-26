<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten'   => "required|string|max:255|unique:admins,ten," . $this->user()?->id,
            'email' => "required|email|unique:admins,email," . $this->user()?->id,
            'mo_ta' => "nullable|string",
            //'so_dien_thoai' => "required|string|regex:/^[0-9]{10,11}$/|unique:admins,so_dien_thoai," . $this->user()?->id
        ];
    }

    public function messages(): array
    {
        return [
            'ten.required'     => 'Tên không được để trống.',
            'ten.string'       => 'Tên phải là chuỗi ký tự.',
            'ten.max'          => 'Tên không được vượt quá 255 ký tự.',
            'ten.unique'       => 'Tên đã tồn tại.',
            'email.required'   => 'Email không được để trống.',
            'email.email'      => 'Email không hợp lệ.',
            'email.unique'     => 'Email đã tồn tại.',
            'mo_ta.nullable'   => 'Mô tả không được để trống.',
            'mo_ta.string'     => 'Mô tả phải là chuỗi ký tự.',
            'so_dien_thoai.required'   => 'Số điện thoại không được để trống.',
            'so_dien_thoai.string'     => 'Số điện thoại phải là chuỗi ký tự.',
            'so_dien_thoai.regex'      => 'Số điện thoại phải là 10 chữ số.',
            'so_dien_thoai.unique'     => 'Số điện thoại đã tồn tại.',
        ];
    }
}
