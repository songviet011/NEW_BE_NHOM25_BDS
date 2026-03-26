<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email')->ignore($this->user()?->getAuthIdentifier()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ten.required' => 'Tên là bắt buộc',
            'ten.string' => 'Tên phải là chuỗi ký tự',
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email phải là định dạng email hợp lệ',
            'email.unique' => 'Email đã tồn tại',
        ];
    }
}
