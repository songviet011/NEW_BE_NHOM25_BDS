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
use App\Http\Controllers\ChucNangController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\ClientHomeController;
use App\Http\Controllers\DiaChiController;
use App\Http\Controllers\LichSuGoiTinController;
use App\Http\Controllers\LoaiBatDongSanController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PhanQuyenController;
use App\Http\Controllers\QuanHuyenController;
use App\Http\Controllers\TinhThanhController;
use App\Http\Controllers\TrainChatController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\KhachHangMiddleware;
use App\Http\Middleware\MoiGioiMiddleware;
use App\Models\LoaiBatDongSan;

//--------------------- AUTH + CHECK TOKEN-----------------------------

Route::prefix('admin')->group(function () {
    Route::post('/dang-nhap', [AdminController::class, 'login']); // đã test postman
    Route::get('/dang-xuat', [AdminController::class, 'logout']);  // đã test postman
    Route::get('/check-token', [AdminController::class, 'checkToken']); // đã test postman
    Route::post('/forgot-password/send-otp', [AdminController::class, 'sendOtp']);
    Route::post('/forgot-password/reset', [AdminController::class, 'resetPassword']);
});

Route::prefix('khach-hang')->group(function () {
    Route::post('/dang-nhap', [KhachHangController::class, 'login']); // đã test postman
    Route::post('/register', [KhachHangController::class, 'register']); // đã test postman
    Route::get('/check-token', [KhachHangController::class, 'checkToken']); // đã test postman
    Route::post('/forgot-password/send-otp', [KhachHangController::class, 'sendOtp']);
    Route::post('/forgot-password/reset', [KhachHangController::class, 'resetPassword']);
});

Route::prefix('moi-gioi')->group(function () {
    Route::post('/dang-nhap', [MoiGioiController::class, 'login']); // đã test postman
    Route::post('/dang-ky', [MoiGioiController::class, 'register']); // đã test postman
    Route::get('/check-token', [MoiGioiController::class, 'checkToken']); // đã test postman
    Route::post('/forgot-password/send-otp', [MoiGioiController::class, 'sendOtp']);
    Route::post('/forgot-password/reset', [MoiGioiController::class, 'resetPassword']);
});

//---------------------------Client Home (Public)--------------------------
Route::prefix('bds')->group(function () {
    // Bất Động Sản
    Route::get('/', [ClientHomeController::class, 'getAllPublic']);
    // Tìm Kiếm 
    Route::post('/tim-kiem', [ClientHomeController::class, 'search']);
});

Route::get('/tinh-thanh', [TinhThanhController::class, 'getTinhThanh']); //đã test postman 
Route::get('/quan-huyen', [QuanHuyenController::class, 'getQuanHuyen']); //đã test postman ?tinh_id=1
Route::get('/loai-bds', [LoaiBatDongSanController::class, 'getAll']);
//----------------------------ADMIN---------------------------

Route::prefix('admin')->middleware('AdminMiddleware')->group(function () {
    // LOGOUT ALL
    Route::get('/dang-xuat-tat-ca', [AdminController::class, 'logoutAll']);

    //PROFILE ADMIN 
    Route::get('/profile', [AdminController::class, 'profile']); // đã test postman
    Route::post('/update-profile', [AdminController::class, 'updateProfile']); // đã test postman

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

    //QUẢN LÝ BẤT ĐỘNG SẢN (xong)
    Route::prefix('bds')->group(function () {
        Route::get('/data', [BatDongSanController::class, 'getData']);
        Route::get('/{id}', [BatDongSanController::class, 'xemChiTietBDS']);
        Route::post('/duyet', [BatDongSanController::class, 'duyetTin']);
        Route::post('/delete', [BatDongSanController::class, 'delete']);
        Route::post('/change-status', [BatDongSanController::class, 'changeStatus']);
        Route::post('/tim-kiem', [BatDongSanController::class, 'searchAdmin']);
    });

    //QUẢN LÝ LOẠI BĐS
    Route::prefix('loai-bds')->group(function () {
        Route::get('/data', [LoaiBatDongSanController::class, 'getData']); // đã test postman
        Route::post('/create', [LoaiBatDongSanController::class, 'store']); // đã test postman
        Route::post('/update/{id}', [LoaiBatDongSanController::class, 'update']); // đã test postman
        Route::delete('/delete/{id}', [LoaiBatDongSanController::class, 'destroy']); // đã test postman
    });

    //QUẢN LÝ GÓI TIN
    Route::prefix('goi-tin')->group(function () {
        Route::get('/data', [GoiTinController::class, 'getData']);
        Route::post('/create', [GoiTinController::class, 'store']);
        Route::post('/update', [GoiTinController::class, 'update']);
        Route::post('/delete', [GoiTinController::class, 'destroy']);
        Route::get('/lich-su-mua', [LichSuGoiTinController::class, 'lichSuMua']);
    });

    //THỐNG KÊ
    Route::post('/doanh-thu', [ThongKeController::class, 'doanhThu']);
    Route::post('/user', [ThongKeController::class, 'user']);

    //GIAO DỊCH
    Route::get('/giao-dich/data', [GiaoDichController::class, 'getData']);

    //CHỨC VỤ
    Route::prefix('chuc-vu')->group(function () {
        Route::get('/data', [ChucVuController::class, 'getData']);
        Route::post('/create', [ChucVuController::class, 'store']);
        Route::post('/update', [ChucVuController::class, 'update']);
        Route::post('/delete', [ChucVuController::class, 'destroy']);
    });
    //CHỨC NĂNG
    Route::get('/chuc-nang/data', [ChucNangController::class, 'getData']);

    // PHÂN QUYỀN
    Route::prefix('phan-quyen')->group(function () {
        Route::get('/data/{id_chuc_vu}', [PhanQuyenController::class, 'getData']);
        Route::post('/chuc-vu/create', [PhanQuyenController::class, 'store']);
        Route::post('/chuc-vu/delete', [PhanQuyenController::class, 'destroy']);
    });
});

//---------------------------MÔI GIỚI---------------------------

Route::prefix('moi-gioi')->middleware('MoiGioiMiddleware')->group(function () {

    //PROFILE MÔI GIỚI
    Route::get('/profile', [MoiGioiController::class, 'profile']);
    Route::post('/update-profile', [MoiGioiController::class, 'updateProfile']);
    Route::post('/update-password', [MoiGioiController::class, 'updatePassword']);
    Route::post('/logout', [MoiGioiController::class, 'logout']);

    //QUẢN LÝ BĐS
    Route::prefix('bds')->group(function () {
        Route::get('/data', [BatDongSanController::class, 'getDataDanhChoMoiGioi']);
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
    Route::post('/update-password', [KhachHangController::class, 'updatePassword']);
    Route::post('/logout', [KhachHangController::class, 'logout']);


    //QUẢN LÝ BĐS
    Route::get('/loai-bds', [LoaiBatDongSanController::class, 'getAll']); //đã test postman

    //TÌM KIẾM ĐỊA CHỈ
    Route::get('/dia-chi', [DiaChiController::class, 'getDiaChi']); // đã test postman ?keyword=123
    Route::get('/dia-chi/{id}', [DiaChiController::class, 'show']);
    Route::get('/bds-khu-vuc', [DiaChiController::class, 'getBdsByKhuVuc']); // đã test postman ?tinh_id=1&quan_id=1

    //MAP (HIỂN THỊ BĐS THEO KHU VỰC)

    Route::get('/map-data', [MapController::class, 'map']);

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
