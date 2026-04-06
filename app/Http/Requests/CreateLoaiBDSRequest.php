<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLoaiBDSRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ten_loai' => 'required|string|max:255|unique:loai_bat_dong_sans,ten_loai',
        ];
    }

    public function messages()
    {
        return [
            'ten_loai.required' => 'Tên loại BĐS là bắt buộc',
            'ten_loai.string' => 'Tên loại BĐS phải là chuỗi',
            'ten_loai.max' => 'Tên loại BĐS không được vượt quá 255 ký tự',
            'ten_loai.unique' => 'Tên loại BĐS đã tồn tại',

        ];
    }
}
