<?php

namespace App\Http\Controllers;

use App\Models\ChucNang;
use Illuminate\Http\Request;

class ChucNangController extends Controller
{
    public function index()
    {
        return ChucNang::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_chuc_nang' => 'required|string',
        ]);

        $chucNang = ChucNang::create($validated);

        return response()->json($chucNang, 201);
    }

    public function update(Request $request, $id)
    {
        $chucNang = ChucNang::findOrFail($id);
        $validated = $request->validate([
            'ten_chuc_nang' => 'string',
        ]);

        $chucNang->update($validated);

        return response()->json($chucNang);
    }

    public function destroy($id)
    {
        ChucNang::findOrFail($id)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }
}

