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
            'ten'   => "required|string|max:255",
            'email' => "required|email|unique:admins,email," . $this->user()?->id,
        ];
    }

    public function messages(): array
    {
        return [
            'ten.required'     => 'Tên không được để trống.',
            'ten.string'       => 'Tên phải là chuỗi ký tự.',
            'ten.max'          => 'Tên không được vượt quá 255 ký tự.',
            'email.required'   => 'Email không được để trống.',
            'email.email'      => 'Email không đúng định dạng.',
            'email.unique'     => 'Email này đã được sử dụng.',
        ];
    }
}