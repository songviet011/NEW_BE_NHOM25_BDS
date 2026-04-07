<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchKhachHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => 'required|string|min:1|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'keyword.required' => 'Vui lòng nhập từ khóa tìm kiếm',
            'keyword.string'   => 'Từ khóa phải là chuỗi ký tự',
            'keyword.min'      => 'Từ khóa không được để trống',
            'keyword.max'      => 'Từ khóa không được vượt quá 255 ký tự',
        ];
    }
}
