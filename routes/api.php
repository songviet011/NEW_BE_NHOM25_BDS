<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BatDongSanController;
use App\Http\Controllers\GiaoDichController;
use App\Http\Controllers\GoiTinController;
use App\Http\Controllers\ThongKeController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\MoiGioiController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\YeuThichController;
use App\Http\Controllers\AIDinhGiaController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\TrainChatController;

// CHECK TOKEN
Route::get('/admin/check-token', [AdminController::class, 'checkToken']);
Route::get('/moi-gioi/check-token', [MoiGioiController::class, 'checkToken']);
Route::get('/khach-hang/check-token', [KhachHangController::class, 'checkToken']);

//---------------------------PUBLIC---------------------------
// Bất Động Sản
Route::get('/bds', [BatDongSanController::class, 'getAllPublic']);
Route::get('/bds/{id}/moi-gioi', [BatDongSanController::class, 'getMoiGioi']);
Route::get('/bds/{id}', [BatDongSanController::class, 'xemChiTiet']);

//TÌM KIẾM
Route::post('/bds/tim-kiem', [BatDongSanController::class, 'search']);


//---------------------------ADMIN---------------------------
//TIM KIẾM CHO ADMIN
Route::post('/admin/bds/tim-kiem', [BatDongSanController::class, 'searchAdmin']);
Route::post('/admin/khach-hang/search', [KhachHangController::class, 'search'])->middleware('AdminMiddleware');
Route::post('/admin/moi-gioi/search', [MoiGioiController::class, 'search'])->middleware('AdminMiddleware');

//Admin 
Route::post('/admin/dang-nhap', [AdminController::class, 'login']);
Route::get('/admin/profile', [AdminController::class, 'profile'])->middleware('AdminMiddleware');
Route::post('/admin/update-profile', [AdminController::class, 'updateProfile'])->middleware('AdminMiddleware');
Route::get('/admin/dang-xuat', [AdminController::class, 'logout']);
//quên mật khẩu

//QUẢN LÝ NGƯỜI DÙNG
Route::prefix('admin')->middleware('AdminMiddleware')->group(function () {
    Route::prefix('khach-hang')->group(function () {
        Route::get('/data', [KhachHangController::class, 'getData']);
        Route::post('/update', [KhachHangController::class, 'update']);
        Route::post('/delete', [KhachHangController::class, 'destroy']);
    });
    Route::prefix('moi-gioi')->group(function () {
        Route::get('/data', [MoiGioiController::class, 'getData']);
        Route::post('/update', [MoiGioiController::class, 'update']);
        Route::post('/delete', [MoiGioiController::class, 'destroy']);
    });
});

//QUẢN LÝ BẤT ĐỘNG SẢN
Route::prefix('admin')->middleware('AdminMiddleware')->group(function () {
    Route::prefix('bds')->group(function () {
        Route::get('/data', [BatDongSanController::class, 'getData']);
        Route::post('/duyet', [BatDongSanController::class, 'duyetTin']);
        Route::post('/delete', [BatDongSanController::class, 'delete']);
    });
});

// BĐS CHANGE STATUS
Route::post('/admin/bds/change-status', [BatDongSanController::class, 'changeStatus']);

//QUẢN LÝ GÓI TIN
Route::get('/admin/goi-tin/data', [GoiTinController::class, 'getData'])->middleware('AdminMiddleware');
Route::post('/admin/goi-tin/create', [GoiTinController::class, 'store'])->middleware('AdminMiddleware');
Route::post('/admin/goi-tin/update', [GoiTinController::class, 'update'])->middleware('AdminMiddleware');
Route::post('/admin/goi-tin/delete', [GoiTinController::class, 'destroy'])->middleware('AdminMiddleware');

// GIAO DỊCH
Route::get('/admin/giao-dich/data', [GiaoDichController::class, 'getData'])->middleware('AdminMiddleware');

//THỐNG KÊ
Route::post('/admin/thong-ke/doanh-thu', [ThongKeController::class, 'doanhThu'])->middleware('AdminMiddleware');
Route::post('/admin/thong-ke/user', [ThongKeController::class, 'user'])->middleware('AdminMiddleware');

// LOGOUT ALL
Route::get('/admin/dang-xuat-tat-ca', [AdminController::class, 'logoutAll']);



//---------------------------MÔI GIỚI---------------------------
//AUTH
Route::post('/moi-gioi/dang-nhap', [MoiGioiController::class, 'login']);
Route::post('/moi-gioi/dang-ky', [MoiGioiController::class, 'register']);
Route::get('/moi-gioi/profile', [MoiGioiController::class, 'profile'])->middleware('MoiGioiMiddleware');
//quên mật khẩu 

//QUẢN LÝ BĐS
Route::get('/moi-gioi/bds/data', [BatDongSanController::class, 'dataMoiGioi'])->middleware('MoiGioiMiddleware');
Route::post('/moi-gioi/bds/create', [BatDongSanController::class, 'store'])->middleware('MoiGioiMiddleware');
Route::post('/moi-gioi/bds/update', [BatDongSanController::class, 'update'])->middleware('MoiGioiMiddleware');
Route::post('/moi-gioi/bds/delete', [BatDongSanController::class, 'destroy'])->middleware('MoiGioiMiddleware');

//MUA GÓI
Route::get('/moi-gioi/goi-tin/data', [GoiTinController::class, 'getAll']);
Route::post('/moi-gioi/goi-tin/mua', [GoiTinController::class, 'muaGoi'])->middleware('MoiGioiMiddleware');

//THÔNG BÁO KHÁCH THẢ TIM
Route::get('/moi-gioi/thong-bao', [ThongBaoController::class, 'getThongBao'])->middleware('MoiGioiMiddleware');

//GIAO DỊCH
Route::get('/moi-gioi/giao-dich/data', [GiaoDichController::class, 'dataMoiGioi'])->middleware('MoiGioiMiddleware');


//---------------------------KHÁCH HÀNG----------------------------
//AUTH
Route::post('/khach-hang/dang-nhap', [KhachHangController::class, 'login']);
Route::post('/khach-hang/register', [KhachHangController::class, 'register']);
//quên mật khẩu

//YÊU THÍCH (THẢ TIM)
Route::post('/khach-hang/bds/yeu-thich', [YeuThichController::class, 'like'])->middleware('KhachHangMiddleware');
Route::get('/khach-hang/bds/yeu-thich/data', [YeuThichController::class, 'getData'])->middleware('KhachHangMiddleware');

//MUA GÓI → TRỞ THÀNH MÔI GIỚI
Route::post('/khach-hang/mua-goi', [GoiTinController::class, 'muaGoi'])->middleware('KhachHangMiddleware');

//MAP (HIỂN THỊ BĐS THEO KHU VỰC)
Route::get('/bds/map-data', [BatDongSanController::class, 'map']);



//------------------------------AI--------------------------
//ĐỊNH GIÁ BĐS
Route::post('/ai/dinh-gia', [AIDinhGiaController::class, 'predictPrice']);

//CHATBOT
Route::post('/chatbot', [TrainChatController::class, 'chat']);
