<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChucVucreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'ten_chuc_vu' => 'required|string|max:255|unique:chuc_vus,ten_chuc_vu',
            'tinh_trang' => 'required|in:0,1',
        ];
    }
    public function messages(): array
    {
        return [
            'ten_chuc_vu.required' => 'Tên chức vụ không được để trống',
            'ten_chuc_vu.string' => 'Tên chức vụ phải là chuỗi',
            'ten_chuc_vu.max' => 'Tên chức vụ không được vượt quá 255 ký tự',
            'ten_chuc_vu.unique' => 'Tên chức vụ đã tồn tại',
            'tinh_trang.required' => 'Tình trạng không được để trống',
            'tinh_trang.in' => 'Tình trạng phải là 0 hoặc 1',
        ];
    }
}
