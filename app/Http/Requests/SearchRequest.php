<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'search.string' => 'Từ khóa tìm kiếm phải là chuỗi ký tự',
            'page.integer' => 'Trang phải là số',
            'page.min' => 'Trang phải lớn hơn 0',
        ];
    }
}
