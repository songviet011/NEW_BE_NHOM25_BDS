#!/bin/bash

# ============================================================
# CURL EXAMPLES - TEST EVENTS VIA API
# ============================================================
# 
# Cách dùng:
#   1. Thay đổi BASE_URL, TOKEN, IDs theo ứng dụng của bạn
#   2. Copy-paste curl command vào terminal
#   3. Hoặc chmod +x curl_test.sh && ./curl_test.sh
#
# ============================================================

# ============================================================
# CONFIG - Thay đổi giá trị này theo server của bạn
# ============================================================
BASE_URL="http://localhost:8000/api"
AUTH_TOKEN="your-sanctum-token-here"  # TODO: Thay bằng token thực tế

# Models IDs - Thay đổi theo database của bạn
BDS_LOAI_ID="1"
BDS_TRANGHAI_ID="1"
MOIGIOI_ID="1"
TINH_ID="1"
QUAN_ID="1"
DIACHI_ID="1"
GOITIN_ID="1"
KHACHHANG_ID="1"

# ============================================================
# TEST 1: TẠO BDS MỚI (BatDongSanCreated Event)
# ============================================================
echo "🔥 TEST 1: TẠO BDS MỚI (FireEvent: BatDongSanCreated)"
echo "=========================================="

curl -X POST "$BASE_URL/bat-dong-san" \
  -H "Authorization: Bearer $AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tieu_de": "Căn hộ cao cấp Midtown từ CURL test",
    "mo_ta": "Căn hộ 2 phòng ngủ, view thoáng mát",
    "gia": 2500000000,
    "dien_tich": 85,
    "loai_id": '${BDS_LOAI_ID}',
    "trang_thai_id": '${BDS_TRANGHAI_ID}',
    "moi_gioi_id": '${MOIGIOI_ID}',
    "tinh_id": '${TINH_ID}',
    "quan_id": '${QUAN_ID}',
    "dia_chi_id": '${DIACHI_ID}',
    "so_phong_ngu": 2,
    "so_phong_tam": 1,
    "is_duyet": false,
    "is_noi_bat": false
  }' \
  -w "\n\nHTTP Status: %{http_code}\n\n"

# Lưu BDS ID từ response để test tiếp
# Ví dụ: BDS_ID=123

echo ""
echo "✅ Để kiểm tra kết quả:"
echo "   1. Xem logs: tail -f storage/logs/laravel.log"
echo "   2. Kiểm tra jobs: SELECT * FROM jobs;"
echo "   3. Chạy queue: php artisan queue:work"
echo ""

# ============================================================
# TEST 2: UPDATE BDS (BatDongSanUpdated Event)
# ============================================================
echo ""
echo "🔥 TEST 2: UPDATE BDS (FireEvent: BatDongSanUpdated)"
echo "=========================================="

# ⚠️ TODO: Lấy BDS_ID từ TEST 1 trước
BDS_ID="1"  # Thay bằng ID thực tế

curl -X PUT "$BASE_URL/bat-dong-san/$BDS_ID" \
  -H "Authorization: Bearer $AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tieu_de": "Căn hộ cao cấp Midtown - GIÁ MỚI",
    "gia": 2600000000,
    "dien_tich": 85
  }' \
  -w "\n\nHTTP Status: %{http_code}\n\n"

echo ""
echo "✅ Để kiểm tra kết quả:"
echo "   • Listener sẽ check giá thay đổi"
echo "   • Dispatcher 2 jobs: SendNotificationJob + AIDefinePriceJob"
echo "   xem bảng jobs sẽ có 2 record"
echo ""

# ============================================================
# TEST 3: TẠO GIAO DỊCH (GiaoDichCreated Event)
# ============================================================
echo ""
echo "🔥 TEST 3: TẠO GIAO DỊCH (FireEvent: GiaoDichCreated)"
echo "=========================================="

curl -X POST "$BASE_URL/giao-dich" \
  -H "Authorization: Bearer $AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "bat_dong_san_id": '${BDS_ID}',
    "moi_gioi_id": '${MOIGIOI_ID}',
    "khach_hang_id": '${KHACHHANG_ID}',
    "gia_giao_dich": 2500000000,
    "mo_ta": "Giao dịch test từ curl"
  }' \
  -w "\n\nHTTP Status: %{http_code}\n\n"

echo ""
echo "✅ Để kiểm tra kết quả:"
echo "   • Listener dispatch SendNotificationJob cho buyer"
echo "   • Dispatcher SendNotificationJob cho seller"
echo "   xem bảng jobs sẽ có 2 SendNotificationJob"
echo ""

# ============================================================
# TEST 4: MUA GÓI TIN (GoiTinPurchased Event)
# ============================================================
echo ""
echo "🔥 TEST 4: MUA GÓI TIN (FireEvent: GoiTinPurchased)"
echo "=========================================="

curl -X POST "$BASE_URL/goi-tin/purchase" \
  -H "Authorization: Bearer $AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "goi_tin_id": '${GOITIN_ID}',
    "moi_gioi_id": '${MOIGIOI_ID}',
    "quantity": 3
  }' \
  -w "\n\nHTTP Status: %{http_code}\n\n"

echo ""
echo "✅ Để kiểm tra kết quả:"
echo "   • Listener ghi lịch sử vào lich_su_goi_tins"
echo "   • Cập nhật quota môi giới"
echo "   kiểm tra bảng lich_su_goi_tins và moi_giois"
echo ""

# ============================================================
# MONITORING COMMANDS (Chạy trên terminal riêng)
# ============================================================
echo ""
echo "════════════════════════════════════════════"
echo "📊 MONITORING COMMANDS (Chạy trong terminal khác)"
echo "════════════════════════════════════════════"
echo ""
echo "1️⃣ Xem logs real-time:"
echo "   tail -f storage/logs/laravel.log"
echo ""
echo "2️⃣ Chạy Queue Worker:"
echo "   php artisan queue:work -v"
echo ""
echo "3️⃣ Test command (interactive):"
echo "   php artisan test:events"
echo ""
echo "4️⃣ Kiểm tra jobs (database):"
echo "   php artisan tinker"
echo "   >>> DB::table('jobs')->count()"
echo ""
echo "5️⃣ Xem failed jobs:"
echo "   php artisan tinker"
echo "   >>> DB::table('failed_jobs')->get()"
echo ""

# ============================================================
# NOTES
# ============================================================
echo ""
echo "⚠️  NOTES:"
echo "   • Thay BASE_URL nếu khác localhost:8000"
echo "   • Thay AUTH_TOKEN bằng token Sanctum thực tế"
echo "   • Thay IDs thành ID thực tế từ database"
echo "   • API phải return 200/201 để event fire successfully"
echo "   • Kiểm tra logs khi API fail"
echo ""
