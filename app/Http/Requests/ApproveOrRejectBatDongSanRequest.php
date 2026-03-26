<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOrRejectBatDongSanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:bat_dong_sans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID BĐS là bắt buộc',
            'id.exists' => 'BĐS không tồn tại',
        ];
    }
}
