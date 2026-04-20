<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MuaGoiTinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'goi_tin_id' => 'required|integer|exists:goi_tins,id',
            'phuong_thuc' => 'nullable|string|in:cash,bank,credit_card,sepay',
        ];
    }

    public function messages(): array
    {
        return [
            'goi_tin_id.required' => 'Vui lòng chọn gói tin',
            'goi_tin_id.exists' => 'Gói tin không tồn tại',
            'phuong_thuc.in' => 'Phương thức thanh toán không hợp lệ',
        ];
    }
}
