<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'type' => 'nullable|in:text,image',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Vui lòng nhập nội dung tin nhắn.',
            'content.string' => 'Nội dung tin nhắn không hợp lệ.',
            'type.in' => 'Loại tin nhắn không hợp lệ.',
        ];
    }
}