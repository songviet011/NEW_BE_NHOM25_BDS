<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThongKeDoanhThuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.date' => 'Ngày bắt đầu phải là định dạng ngày hợp lệ',
            'end_date.date' => 'Ngày kết thúc phải là định dạng ngày hợp lệ',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
        ];
    }
}
