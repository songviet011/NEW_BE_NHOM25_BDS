<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'moi_gioi_id' => 'required|integer|exists:moi_giois,id',
            'bat_dong_san_id' => 'nullable|integer|exists:bat_dong_sans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'moi_gioi_id.required' => 'Vui lòng chọn môi giới.',
            'moi_gioi_id.integer' => 'ID môi giới không hợp lệ.',
            'moi_gioi_id.exists' => 'Môi giới không tồn tại.',
            'bat_dong_san_id.integer' => 'ID bất động sản không hợp lệ.',
            'bat_dong_san_id.exists' => 'Bất động sản không tồn tại.',
        ];
    }
}