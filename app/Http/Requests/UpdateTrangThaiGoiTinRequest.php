<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTrangThaiGoiTinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:goi_tins,id',
            'trang_thai' => 'required|string|in:active,inactive,archived', //hoạt động, không hoạt động, đã lưu trữ
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Vui lòng truyền id gói tin',
            'id.integer' => 'ID gói tin phải là số',
            'id.exists' => 'Gói tin không tồn tại',
            'trang_thai.required' => 'Vui lòng truyền trạng thái mới',
            'trang_thai.string' => 'Trạng thái phải là chuỗi',
            'trang_thai.in' => 'Trạng thái không hợp lệ (phải là active, inactive hoặc archived)',
        ];
    }
}
