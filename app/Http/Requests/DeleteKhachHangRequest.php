<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteKhachHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:khach_hangs,id',
        ];
    }
    public function messages(): array
    {
        return [
            'id.required' => 'Vui lòng cung cấp ID khách hàng',
            'id.integer' => 'ID phải là số nguyên',
            'id.exists' => 'Khách hàng không tồn tại',
        ];
    }
}
