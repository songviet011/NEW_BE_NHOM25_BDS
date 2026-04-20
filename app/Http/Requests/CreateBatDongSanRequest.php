<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBatDongSanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tieu_de' => 'nullable|string|max:255',
            'gia' => 'required|numeric|min:0',
            'dien_tich' => 'required|numeric|min:0',
            'loai_id' => 'required|integer|exists:loai_bat_dong_sans,id',
            'trang_thai_id' => 'required|integer|exists:trang_thai_bat_dong_sans,id',
            'mo_ta' => 'nullable|string',
            'tinh_id' => 'nullable|integer|exists:tinh_thanhs,id',
            'quan_id' => 'nullable|integer|exists:quan_huyens,id',
            'dia_chi_id' => 'nullable|integer|exists:dia_chis,id',
            'so_phong_ngu' => 'nullable|integer|min:0',
            'so_phong_tam' => 'nullable|integer|min:0',
            'is_noi_bat' => 'nullable|boolean',
            'hinh_anh'        => 'nullable|array|max:10',
            'hinh_anh.*'      => 'image|mimes:jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'tieu_de.string' => 'Tiêu đề phải là chuỗi ký tự',
            'tieu_de.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'gia.required' => 'Giá là bắt buộc',
            'gia.numeric' => 'Giá phải là số',
            'dien_tich.required' => 'Diện tích là bắt buộc',
            'dien_tich.numeric' => 'Diện tích phải là số',
            'loai_id.required' => 'Loại BĐS là bắt buộc',
            'loai_id.exists' => 'Loại BĐS không tồn tại',
            'trang_thai_id.required' => 'Trạng thái là bắt buộc',
            'trang_thai_id.exists' => 'Trạng thái không tồn tại',
            'tinh_id.exists' => 'Tỉnh không tồn tại',
            'quan_id.exists' => 'Quận không tồn tại',
            'dia_chi_id.exists' => 'Địa chỉ không tồn tại',
            'so_phong_ngu.integer' => 'Số phòng ngủ phải là số nguyên',
            'so_phong_ngu.min' => 'Số phòng ngủ không được âm',
            'so_phong_tam.integer' => 'Số phòng tắm phải là số nguyên',
            'so_phong_tam.min' => 'Số phòng tắm không được âm',
            'is_noi_bat.boolean' => 'Giá trị is_noi_bat phải là true hoặc false',
            'mo_ta.string' => 'Mô tả phải là chuỗi ký tự',
            'mo_ta.max' => 'Mô tả không được vượt quá 255 ký tự',
            'hinh_anh.array'        => 'Hình ảnh phải là danh sách file',
            'hinh_anh.max'          => 'Chỉ được upload tối đa 10 ảnh',
            'hinh_anh.*.image'      => 'File phải là hình ảnh',
            'hinh_anh.*.mimes'      => 'Hình ảnh phải có định dạng: jpg, jpeg, png',
            'hinh_anh.*.max'        => 'Mỗi ảnh không được vượt quá 5MB',
        ];
    }
}
