<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiaChiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy tên method đang được gọi từ route
        $action = $this->route()->getActionMethod();

        return match ($action) {
            // 1. Tìm kiếm theo keyword - chỉ cần keyword
            'getDiaChi' => [
                'keyword' => 'nullable|string|max:255',
            ],

            // 2. Lọc BĐS theo khu vực - cần tinh_id hoặc quan_id (optional)
            'getBdsByKhuVuc' => [
                'tinh_id' => 'nullable|integer|exists:tinh_thanhs,id',
                'quan_id' => 'nullable|integer|exists:quan_huyens,id',
            ],

            // 3. Xem chi tiết theo ID - validate id từ URL
            'show' => [
                'id' => 'required|integer|exists:dia_chis,id',
            ],

            // 4. Các method khác (store/update) - rules đầy đủ
            default => [
                'tinh_id'           => 'required|integer|exists:tinh_thanhs,id',
                'quan_id'           => 'required|integer|exists:quan_huyens,id',
                'dia_chi_chi_tiet'  => 'required|string|max:255',
                'lat'               => 'required|numeric|between:-90,90',
                'lng'               => 'required|numeric|between:-180,180',
            ],
        };
    }

    public function messages(): array
    {
        return [
            // Messages cho search
            'keyword.string'   => 'Từ khóa phải là chuỗi ký tự',
            'keyword.max'      => 'Từ khóa không được vượt quá 255 ký tự',

            // Messages cho filter khu vực
            'tinh_id.integer'  => 'Mã tỉnh phải là số nguyên',
            'tinh_id.exists'   => 'Tỉnh thành không tồn tại',
            'quan_id.integer'  => 'Mã quận phải là số nguyên',
            'quan_id.exists'   => 'Quận huyện không tồn tại',

            // Messages cho create/update
            'tinh_id.required'          => 'Vui lòng chọn tỉnh thành.',
            'quan_id.required'          => 'Vui lòng chọn quận huyện.',
            'dia_chi_chi_tiet.required' => 'Vui lòng nhập địa chỉ chi tiết.',
            'dia_chi_chi_tiet.string'   => 'Địa chỉ chi tiết phải là chuỗi.',
            'dia_chi_chi_tiet.max'      => 'Địa chỉ không được vượt quá 255 ký tự.',
            'lat.required'              => 'Vui lòng nhập vĩ độ.',
            'lat.numeric'               => 'Vĩ độ phải là số.',
            'lat.between'               => 'Vĩ độ phải từ -90 đến 90.',
            'lng.required'              => 'Vui lòng nhập kinh độ.',
            'lng.numeric'               => 'Kinh độ phải là số.',
            'lng.between'               => 'Kinh độ phải từ -180 đến 180.',
        ];
    }
}
