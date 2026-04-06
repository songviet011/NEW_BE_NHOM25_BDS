<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLoaiBDSRequest;
use App\Http\Requests\UpdateLoaiBDSRequest;
use App\Models\LoaiBatDongSan;
use App\Models\TinhThanh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoaiBatDongSanController extends Controller
{
    //Admin
    // Lấy dữ liệu loại BĐS 
    public function getData(Request $request)
    {
        $data = LoaiBatDongSan::all();
        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Có lỗi xảy ra"
            ]);
        }
    }

    //Tạo loại BĐS mới
    public function store(CreateLoaiBDSRequest $request)
    {
        $data = LoaiBatDongSan::create([
            'ten_loai' => $request->ten_loai,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Loại BĐS đã được tạo thành công',
            'data' => $data
        ]);
    }

    //Cập nhật loại BĐS
    public function update(UpdateLoaiBDSRequest $request, $id)
    {
        $data = LoaiBatDongSan::findOrFail($id);
        $data->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Loại BĐS đã được cập nhật thành công',
            'data' => $data
        ]);
    }

    //Xóa loại BĐS
    public function destroy($id)
    {
        $data = LoaiBatDongSan::findOrFail($id);
        $data->delete();
        return response()->json([
            'status' => true,
            'message' => 'Xóa loại BĐS thành công'
        ]);
    }
}
