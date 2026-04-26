<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class XacThucMaQuenMatKhauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'code'  => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ',
            'code.required' => 'Mã xác nhận không được để trống',
        ];
    }
}
