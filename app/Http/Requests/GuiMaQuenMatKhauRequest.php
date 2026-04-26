<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GuiMaQuenMatKhauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không hợp lệ'
        ];
    }
}
