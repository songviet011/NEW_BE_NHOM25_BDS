<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteMoiGioiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'id' => 'required|integer|exists:moi_giois,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID là bắt buộc',
            'id.integer'  => 'ID phải là số',
            'id.exists'   => 'Môi giới không tồn tại',
        ];
    }
}
