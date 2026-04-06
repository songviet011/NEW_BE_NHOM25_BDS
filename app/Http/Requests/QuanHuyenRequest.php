<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuanHuyenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Define validation rules here
        ];
    }
}
