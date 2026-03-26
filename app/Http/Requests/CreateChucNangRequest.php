<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateChucNangRequest extends FormRequest
{
    public function authorize()
    {
        return auth('sanctum')->check();
    }

    public function rules()
    {
        return [
            'ten_chuc_nang' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'ten_chuc_nang.required' => 'Tên chức năng là bắt buộc',
            'ten_chuc_nang.string' => 'Tên chức năng phải là văn bản',
            'ten_chuc_nang.max' => 'Tên chức năng không được vượt quá 255 ký tự',
        ];
    }
}
