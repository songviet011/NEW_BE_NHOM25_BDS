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


//--------------------- AUTH + CHECK TOKEN-----------------------------

Route::prefix('admin')->group(function () {
    Route::post('/dang-nhap', [AdminController::class, 'login']);
    Route::get('/dang-xuat', [AdminController::class, 'logout']);
    Route::get('/check-token', [AdminController::class, 'checkToken']);
});

Route::prefix('khach-hang')->group(function () {
    Route::post('/dang-nhap', [KhachHangController::class, 'login']);
    Route::post('/register', [KhachHangController::class, 'register']);
    Route::get('/check-token', [KhachHangController::class, 'checkToken']);
});

Route::prefix('moi-gioi')->group(function () {
    Route::post('/dang-nhap', [MoiGioiController::class, 'login']);
    Route::post('/dang-ky', [MoiGioiController::class, 'register']);
    Route::get('/check-token', [MoiGioiController::class, 'checkToken']);
});

//---------------------------PUBLIC---------------------------

Route::prefix('bds')->group(function () {
    // Bất Động Sản
    Route::get('/', [BatDongSanController::class, 'getAllPublic']);
    // Tìm Kiếm 
    Route::post('/tim-kiem', [BatDongSanController::class, 'search']);
    // Lấy môi giới theo BDS
    Route::get('/{id}/moi-gioi', [BatDongSanController::class, 'getMoiGioi']);
    // Chi tiết BDS
    Route::get('/{id}', [BatDongSanController::class, 'xemChiTiet']);
    //MAP (HIỂN THỊ BĐS THEO KHU VỰC)
    Route::get('/map-data', [BatDongSanController::class, 'map']);
});

//----------------------------ADMIN---------------------------

Route::prefix('admin')->middleware('AdminMiddleware')->group(function () {
    // LOGOUT ALL
    Route::get('/dang-xuat-tat-ca', [AdminController::class, 'logoutAll']);

    //PROFILE ADMIN 
    Route::get('/profile', [AdminController::class, 'profile']);
    Route::post('/update-profile', [AdminController::class, 'updateProfile']);

    //TIM KIẾM CHO ADMIN
    Route::post('/khach-hang/search', [KhachHangController::class, 'search']);
    Route::post('/moi-gioi/search', [MoiGioiController::class, 'search']);

    //QUẢN LÝ KHÁCH HÀNG
    Route::prefix('khach-hang')->group(function () {
        Route::get('/data', [KhachHangController::class, 'getData']);
        Route::post('/update', [KhachHangController::class, 'update']);
        Route::post('/delete', [KhachHangController::class, 'destroy']);
    });

    //QUẢN LÝ MÔI GIỚI
    Route::prefix('moi-gioi')->group(function () {
        Route::get('/data', [MoiGioiController::class, 'getData']);
        Route::post('/update', [MoiGioiController::class, 'update']);
        Route::post('/delete', [MoiGioiController::class, 'destroy']);
    });

    //QUẢN LÝ BẤT ĐỘNG SẢN
    Route::prefix('bds')->group(function () {
        Route::get('/data', [BatDongSanController::class, 'getData']);
        Route::post('/duyet', [BatDongSanController::class, 'duyetTin']);
        Route::post('/delete', [BatDongSanController::class, 'delete']);
        Route::post('/change-status', [BatDongSanController::class, 'changeStatus']);
        Route::post('/tim-kiem', [BatDongSanController::class, 'searchAdmin']);
    });

    //QUẢN LÝ GÓI TIN
    Route::prefix('goi-tin')->group(function () {
        Route::get('/data', [GoiTinController::class, 'getData']);
        Route::post('/create', [GoiTinController::class, 'store']);
        Route::post('/update', [GoiTinController::class, 'update']);
        Route::post('/delete', [GoiTinController::class, 'destroy']);
    });

    //THỐNG KÊ
    Route::post('/doanh-thu', [ThongKeController::class, 'doanhThu']);
    Route::post('/user', [ThongKeController::class, 'user']);

    //GIAO DỊCH
    Route::get('/giao-dich/data', [GiaoDichController::class, 'getData']);
});

//---------------------------MÔI GIỚI---------------------------

Route::prefix('moi-gioi')->middleware('MoiGioiMiddleware')->group(function () {

    //PROFILE MÔI GIỚI
    Route::get('/profile', [MoiGioiController::class, 'profile']);
    Route::post('/update-profile', [MoiGioiController::class, 'updateProfile']);

    //QUẢN LÝ BĐS
    Route::prefix('bds')->group(function () {
        Route::get('/data', [BatDongSanController::class, 'dataMoiGioi']);
        Route::post('/create', [BatDongSanController::class, 'store']);
        Route::post('/update', [BatDongSanController::class, 'update']);
        Route::post('/delete', [BatDongSanController::class, 'destroy']);
    });

    //MUA GÓI
    Route::get('/goi-tin/data', [GoiTinController::class, 'getAll']);
    Route::post('/goi-tin/mua', [GoiTinController::class, 'muaGoi']);

    //THÔNG BÁO KHÁCH THẢ TIM
    Route::get('/thong-bao', [ThongBaoController::class, 'getThongBao']);

    //GIAO DỊCH
    Route::get('/giao-dich/data', [GiaoDichController::class, 'dataMoiGioi']);
});

//---------------------------KHÁCH HÀNG----------------------------

Route::prefix('khach-hang')->middleware('KhachHangMiddleware')->group(function () {

    //PROFILE KHÁCH HÀNG
    Route::get('/profile', [KhachHangController::class, 'profile']); 
    Route::post('/update-profile', [KhachHangController::class, 'updateProfile']);

    //YÊU THÍCH (THẢ TIM)
    Route::post('/bds/yeu-thich', [YeuThichController::class, 'like']);
    Route::get('/bds/yeu-thich/data', [YeuThichController::class, 'getData']);

    //MUA GÓI → TRỞ THÀNH MÔI GIỚI
    Route::post('/mua-goi', [GoiTinController::class, 'muaGoi']);
});

//------------------------------AI--------------------------
//ĐỊNH GIÁ BĐS
Route::post('/ai/dinh-gia', [AIDinhGiaController::class, 'predictPrice']);

//CHATBOT
Route::post('/chatbot', [TrainChatController::class, 'chat']);
