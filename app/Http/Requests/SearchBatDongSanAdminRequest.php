<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchBatDongSanAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'keyword' => 'required|string|max:255',
        ];
    }
    
    public function messages(): array
    {
        return [
            'keyword.required' => 'Vui lòng nhập từ khóa tìm kiếm.',
            'keyword.string' => 'Từ khóa phải là một chuỗi.',
            'keyword.max' => 'Từ khóa không được vượt quá 255 ký tự.',
        ];
    }
}
