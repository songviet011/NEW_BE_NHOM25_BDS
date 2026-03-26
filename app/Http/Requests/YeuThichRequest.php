<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class YeuThichRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bds_id' => 'required|integer|exists:bat_dong_sans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'bds_id.required' => 'Vui lòng truyền id bất động sản',
            'bds_id.integer' => 'ID BĐS phải là số',
            'bds_id.exists' => 'BĐS không tồn tại',
        ];
    }
}
