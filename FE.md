# Đặc tả Frontend cho hệ thống Bất Động Sản

## 1. Mục tiêu

Tạo một frontend web responsive bằng **Bootstrap 5** cho hệ thống sàn giao dịch bất động sản. Hệ thống có 3 vai trò chính:

1. **Admin**
2. **Môi Giới**
3. **Khách Hàng**

Ngoài ra còn có khu vực **Public/Guest** để xem danh sách bất động sản công khai trước khi đăng nhập.

Frontend cần phục vụ đồng thời:

1. Website tra cứu bất động sản cho người dùng phổ thông.
2. Khu vực tài khoản cho khách hàng.
3. Khu vực quản lý tin đăng cho môi giới.
4. Khu vực quản trị hệ thống cho admin.

Ngôn ngữ giao diện: **Tiếng Việt**.

## 2. Công nghệ và phong cách giao diện

Yêu cầu tạo giao diện theo các nguyên tắc sau:

1. Dùng **Bootstrap 5** làm nền tảng giao diện.
2. Dùng **Bootstrap Icons** cho icon.
3. Không dùng phong cách quá hiện đại kiểu glassmorphism; ưu tiên giao diện rõ ràng, sạch, dễ dùng, thiên về dashboard doanh nghiệp.
4. Tất cả trang phải responsive tốt trên desktop, tablet và mobile.
5. Dùng layout chuẩn Bootstrap như `container`, `container-fluid`, `row`, `col`, `card`, `table`, `badge`, `modal`, `offcanvas`, `toast`, `pagination`, `nav-tabs`, `dropdown`.
6. Form phải có validate trực quan, thông báo lỗi ngay dưới input.
7. Các thao tác tạo, sửa, xóa, duyệt, đổi trạng thái phải có modal xác nhận hoặc toast feedback.
8. Màn hình tải dữ liệu nên có skeleton hoặc spinner.
9. Trang trống dữ liệu phải có empty state đẹp, rõ ý nghĩa.

## 3. Định hướng nhận diện giao diện

### 3.1 Tông màu

1. Màu chủ đạo: xanh navy hoặc xanh dương đậm.
2. Màu phụ: xanh ngọc nhạt hoặc cyan nhẹ.
3. Màu nhấn:
4. Thành công: xanh lá.
5. Chờ xử lý: vàng/cam.
6. Lỗi hoặc bị từ chối: đỏ.
7. Thông tin trung tính: xám nhạt và trắng.

### 3.2 Font và cảm giác

1. Giao diện cần cảm giác tin cậy, nghiêm túc, phù hợp lĩnh vực bất động sản.
2. Dùng typography rõ ràng, tiêu đề lớn, giá tiền nổi bật.
3. Card bất động sản nên có ảnh lớn, badge trạng thái, giá rõ, diện tích rõ.

## 4. Kiến trúc tổng thể frontend

Chia frontend thành 4 khu vực:

1. **Public**
2. **Khách Hàng**
3. **Môi Giới**
4. **Admin**

### 4.1 Khu vực Public

Bao gồm:

1. Trang chủ.
2. Danh sách bất động sản công khai.
3. Chi tiết bất động sản.
4. Tìm kiếm bất động sản.
5. Đăng nhập/đăng ký khách hàng.
6. Đăng nhập/đăng ký môi giới.
7. Quên mật khẩu theo từng vai trò.

### 4.2 Khu vực Khách Hàng

Bao gồm:

1. Hồ sơ cá nhân.
2. Đổi mật khẩu.
3. Danh sách bất động sản yêu thích.
4. Tìm kiếm địa chỉ.
5. Xem bất động sản theo khu vực.
6. Xem bất động sản trên bản đồ.
7. Tìm bất động sản gần vị trí.
8. Mua gói để nâng cấp thành môi giới.

### 4.3 Khu vực Môi Giới

Bao gồm:

1. Hồ sơ cá nhân.
2. Đổi mật khẩu.
3. Quản lý bất động sản của tôi.
4. Tạo tin đăng mới.
5. Cập nhật tin đăng.
6. Xóa tin đăng.
7. Xem thông báo khách hàng thả tim.
8. Xem gói tin.
9. Thanh toán mua gói qua SePay.
10. Theo dõi số tin còn lại và ngày hết hạn gói.

### 4.4 Khu vực Admin

Bao gồm:

1. Dashboard tổng quan.
2. Quản lý khách hàng.
3. Quản lý môi giới.
4. Quản lý bất động sản.
5. Duyệt tin.
6. Đổi trạng thái bất động sản.
7. Quản lý loại bất động sản.
8. Quản lý gói tin.
9. Xem lịch sử mua gói.
10. Quản lý chức vụ.
11. Quản lý chức năng.
12. Phân quyền theo chức vụ.
13. Hồ sơ admin.
14. Đăng xuất một thiết bị hoặc tất cả thiết bị.

## 5. Layout chuẩn cho từng khu vực

### 5.1 Public Layout

1. Header cố định trên cùng.
2. Logo bên trái.
3. Menu chính: Trang chủ, Bất động sản, Tìm kiếm, Đăng nhập, Đăng ký.
4. Nút CTA nổi bật: “Đăng tin”, “Đăng ký môi giới”, “Xem bản đồ”.
5. Hero section có form tìm kiếm nhanh.
6. Phần danh sách bất động sản hiển thị dạng grid card.
7. Footer có thông tin liên hệ, điều khoản, hỗ trợ.

### 5.2 Layout cho Khách Hàng và Môi Giới

1. Dùng layout dashboard 2 cột.
2. Sidebar trái cố định trên desktop, offcanvas trên mobile.
3. Topbar có avatar, tên người dùng, chuông thông báo, dropdown tài khoản.
4. Nội dung chính ở bên phải.
5. Mỗi trang dùng `card` để nhóm nội dung.

### 5.3 Layout cho Admin

1. Sidebar trái nhiều nhóm menu.
2. Topbar có ô tìm kiếm nhanh, avatar admin, nút đăng xuất.
3. Dashboard dùng nhiều card KPI và biểu đồ.
4. Các module quản lý dùng table, filter bar, modal form.

## 6. Menu chi tiết theo vai trò

### 6.1 Menu Public

1. Trang chủ
2. Danh sách bất động sản
3. Tìm kiếm nâng cao
4. Đăng nhập khách hàng
5. Đăng ký khách hàng
6. Đăng nhập môi giới
7. Đăng ký môi giới

### 6.2 Menu Khách Hàng

1. Trang khám phá bất động sản
2. Yêu thích
3. Bản đồ khu vực
4. Hồ sơ cá nhân
5. Đổi mật khẩu
6. Nâng cấp thành môi giới
7. Đăng xuất

### 6.3 Menu Môi Giới

1. Tổng quan
2. Bất động sản của tôi
3. Đăng tin mới
4. Gói tin
5. Thông báo
6. Hồ sơ cá nhân
7. Đổi mật khẩu
8. Đăng xuất

### 6.4 Menu Admin

1. Dashboard
2. Quản lý khách hàng
3. Quản lý môi giới
4. Quản lý bất động sản
5. Quản lý loại bất động sản
6. Quản lý gói tin
7. Lịch sử mua gói
8. Giao dịch
9. Chức vụ
10. Chức năng
11. Phân quyền
12. Hồ sơ admin
13. Đăng xuất

## 7. Dữ liệu nghiệp vụ chính cần hiển thị

### 7.1 Bất động sản

Trường dữ liệu chính:

1. `id`
2. `tieu_de`
3. `mo_ta`
4. `gia`
5. `dien_tich`
6. `loai_id`
7. `trang_thai_id`
8. `moi_gioi_id`
9. `dia_chi_id`
10. `so_phong_ngu`
11. `so_phong_tam`
12. `is_duyet`
13. `is_noi_bat`
14. danh sách `hinh_anh`
15. thông tin `địa chỉ`, `tỉnh`, `quận`
16. thông tin `môi giới`

### 7.2 Môi giới

Trường dữ liệu chính:

1. `id`
2. `ten`
3. `email`
4. `so_dien_thoai`
5. `avatar`
6. `mo_ta`
7. `zalo_link`
8. `is_active`
9. `so_tin_con_lai`
10. `ngay_het_han_goi`

### 7.3 Khách hàng

Trường dữ liệu chính:

1. `id`
2. `ten`
3. `email`
4. `so_dien_thoai`
5. `is_active`

### 7.4 Gói tin

Trường dữ liệu chính:

1. `id`
2. `ten_goi`
3. `gia`
4. `so_ngay`
5. `so_luong_tin`

### 7.5 Giao dịch

Trường dữ liệu chính:

1. `id`
2. `moi_gioi_id`
3. `goi_tin_id`
4. `so_tien`
5. `phuong_thuc`
6. `trang_thai`
7. `ma_giao_dich`
8. `ma_sepay_txn_ref`
9. `created_at`

### 7.6 Địa chỉ

Trường dữ liệu chính:

1. `tinh_id`
2. `quan_id`
3. `dia_chi_chi_tiet`
4. `lat`
5. `lng`

## 8. Màn hình Public chi tiết

### 8.1 Trang chủ

Trang chủ cần có:

1. Hero section lớn với ảnh nền bất động sản.
2. Thanh tìm kiếm nhanh ngay giữa hero.
3. Các ô filter nhanh:
4. Loại bất động sản.
5. Tỉnh thành.
6. Giá tối thiểu.
7. Giá tối đa.
8. Từ khóa tiêu đề.
9. Khối “Bất động sản nổi bật”.
10. Khối “Bất động sản mới nhất”.
11. Khối “Danh mục loại bất động sản”.
12. Banner mời đăng ký môi giới.
13. Footer có thông tin công ty và hỗ trợ.

### 8.2 Danh sách bất động sản công khai

Trang danh sách cần có:

1. Sidebar filter bên trái trên desktop.
2. Filter mở bằng offcanvas trên mobile.
3. Vùng kết quả dạng card grid.
4. Phân trang ở cuối danh sách.
5. Sắp xếp theo:
6. Giá tăng dần.
7. Giá giảm dần.
8. Mới nhất.
9. Nổi bật.

Mỗi card bất động sản hiển thị:

1. Ảnh đại diện.
2. Tiêu đề.
3. Giá.
4. Diện tích.
5. Loại bất động sản.
6. Tên môi giới.
7. Badge “Nổi bật” nếu `is_noi_bat = true`.
8. Badge trạng thái duyệt hoặc trạng thái giao dịch nếu cần.
9. Nút xem chi tiết.
10. Nút thả tim nếu đã đăng nhập khách hàng.

### 8.3 Chi tiết bất động sản

Trang chi tiết cần có:

1. Gallery ảnh lớn ở đầu trang.
2. Thông tin chính ở cột trái.
3. Box liên hệ môi giới ở cột phải.
4. Section mô tả chi tiết.
5. Section thông tin kỹ thuật.
6. Section địa chỉ và bản đồ vị trí.
7. Section bất động sản liên quan.

Thông tin kỹ thuật cần hiển thị:

1. Giá.
2. Diện tích.
3. Số phòng ngủ.
4. Số phòng tắm.
5. Loại bất động sản.
6. Trạng thái bất động sản.
7. Tỉnh.
8. Quận.
9. Địa chỉ chi tiết.

Box môi giới cần có:

1. Avatar.
2. Tên môi giới.
3. Mô tả ngắn.
4. Số điện thoại.
5. Link Zalo.
6. Nút gọi điện.
7. Nút chat Zalo.
8. Nút thả tim.

### 8.4 Hành vi đặc biệt khi khách chưa đăng nhập

Backend hiện tại có logic ẩn thông tin khi là guest. Frontend phải hỗ trợ rõ hành vi này:

1. Ẩn số điện thoại môi giới.
2. Ẩn link Zalo.
3. Ẩn địa chỉ chi tiết.
4. Mô tả bị cắt ngắn.
5. Hiển thị badge hoặc alert: “Đăng nhập để xem thông tin liên hệ đầy đủ”.
6. Hiển thị nút CTA dẫn tới đăng nhập/đăng ký.

Nếu API trả `is_guest_view = true` thì UI phải bật chế độ khóa dữ liệu liên hệ.

## 9. Màn hình Khách Hàng chi tiết

### 9.1 Đăng nhập khách hàng

Form gồm:

1. Email
2. Mật khẩu
3. Checkbox ghi nhớ đăng nhập nếu muốn
4. Nút đăng nhập
5. Link quên mật khẩu
6. Link đăng ký tài khoản mới

### 9.2 Đăng ký khách hàng

Form gồm:

1. Họ tên
2. Email
3. Số điện thoại
4. Mật khẩu
5. Nhập lại mật khẩu
6. Checkbox đồng ý điều khoản
7. Nút đăng ký

### 9.3 Quên mật khẩu khách hàng

Luồng 2 bước:

1. Bước 1 nhập email để nhận OTP.
2. Bước 2 nhập email, OTP và mật khẩu mới.

Giao diện nên tách thành stepper hoặc 2 card liền mạch.

### 9.4 Hồ sơ khách hàng

Trang hồ sơ gồm:

1. Card thông tin cơ bản.
2. Form cập nhật họ tên.
3. Form cập nhật số điện thoại.
4. Có thể cho phép cập nhật password trực tiếp ở form riêng.
5. Nút lưu thay đổi.

### 9.5 Đổi mật khẩu khách hàng

Form gồm:

1. Mật khẩu cũ
2. Mật khẩu mới
3. Nhập lại mật khẩu mới
4. Nút cập nhật

### 9.6 Danh sách yêu thích

Trang này hiển thị danh sách bất động sản mà khách hàng đã thả tim.

Giao diện cần có:

1. Grid card hoặc table card responsive.
2. Ảnh đại diện bất động sản.
3. Tiêu đề.
4. Giá.
5. Môi giới phụ trách.
6. Nút xem chi tiết.
7. Nút bỏ yêu thích.

### 9.7 Tìm kiếm địa chỉ

Tạo một màn hình hoặc module search địa chỉ gồm:

1. Ô nhập từ khóa.
2. Gợi ý realtime tối đa 5 kết quả.
3. Kết quả hiển thị:
4. Địa chỉ chi tiết.
5. Quận.
6. Tỉnh.
7. Nút “Xem bất động sản trong khu vực”.

### 9.8 Bất động sản theo khu vực

Trang này hiển thị danh sách bất động sản đã duyệt theo tỉnh/quận.

Giao diện cần có:

1. Bộ lọc tỉnh thành.
2. Dropdown quận huyện phụ thuộc tỉnh.
3. Danh sách kết quả dạng card.
4. Phân trang 20 item.

### 9.9 Bản đồ bất động sản

Đây là màn hình rất quan trọng cho khách hàng.

Yêu cầu giao diện:

1. Chia 2 cột:
2. Cột trái là bộ lọc.
3. Cột phải là bản đồ lớn.
4. Marker hiển thị các bất động sản có tọa độ.
5. Click marker mở popup nhỏ.
6. Popup hiển thị:
7. Ảnh đại diện.
8. Tiêu đề.
9. Giá format đẹp.
10. Diện tích.
11. Địa chỉ.
12. Tên môi giới.
13. Số điện thoại môi giới nếu đã đăng nhập.
14. Nút xem chi tiết.

Bộ lọc trên bản đồ gồm:

1. Khoảng giá.
2. Loại bất động sản.
3. Khu vực đang xem trên map.
4. Nút reset filter.

### 9.10 Tìm bất động sản gần vị trí

Có thể làm thành tab phụ trong màn hình bản đồ.

Form tìm gần vị trí gồm:

1. Vĩ độ.
2. Kinh độ.
3. Bán kính km.
4. Nút tìm kiếm.

Kết quả hiển thị:

1. Danh sách các bất động sản gần vị trí.
2. Khoảng cách ước tính.
3. Nút xem chi tiết trên bản đồ.

### 9.11 Mua gói để nâng cấp thành môi giới

Tạo một trang CTA nâng cấp tài khoản.

Giao diện cần có:

1. Giới thiệu quyền lợi khi trở thành môi giới.
2. Danh sách gói tin dưới dạng pricing card.
3. Mỗi card hiển thị:
4. Tên gói.
5. Giá.
6. Số ngày.
7. Số lượng tin được đăng.
8. Nút mua ngay.

Lưu ý:

1. Route nâng cấp đã khai báo nhưng backend chưa hoàn thiện đầy đủ.
2. Frontend vẫn nên thiết kế sẵn trang pricing đẹp và có modal xác nhận mua gói.

## 10. Màn hình Môi Giới chi tiết

### 10.1 Đăng nhập môi giới

Form gồm:

1. Email
2. Mật khẩu
3. Nút đăng nhập
4. Link quên mật khẩu
5. Link đăng ký môi giới

### 10.2 Đăng ký môi giới

Form gồm:

1. Họ tên
2. Email
3. Số điện thoại
4. Mật khẩu
5. Nhập lại mật khẩu
6. Link Zalo
7. Mô tả bản thân
8. Nút đăng ký

### 10.3 Quên mật khẩu môi giới

Luồng giống khách hàng:

1. Gửi OTP qua email.
2. Nhập OTP và mật khẩu mới.

### 10.4 Dashboard môi giới

Trang tổng quan môi giới nên có:

1. Card số tin còn lại.
2. Card ngày hết hạn gói.
3. Card tổng số tin đã đăng.
4. Card số tin đang chờ duyệt.
5. Card số tin nổi bật.
6. Khu vực thông báo mới nhất.
7. Danh sách bất động sản gần đây.

### 10.5 Hồ sơ môi giới

Form hiển thị và chỉnh sửa:

1. Tên
2. Email
3. Số điện thoại
4. Link Zalo
5. Mô tả
6. Avatar giả lập hoặc khu vực avatar
7. Trạng thái gói hiện tại
8. Số tin còn lại
9. Ngày hết hạn gói

### 10.6 Đổi mật khẩu môi giới

Form gồm:

1. Mật khẩu cũ
2. Mật khẩu mới
3. Nhập lại mật khẩu mới
4. Nút cập nhật

### 10.7 Danh sách bất động sản của tôi

Đây là màn hình table quản lý chính của môi giới.

Bảng cần có cột:

1. Tiêu đề
2. Giá
3. Diện tích
4. Địa chỉ
5. Quận
6. Tỉnh
7. Loại bất động sản
8. Trạng thái
9. Tình trạng duyệt
10. Ngày tạo
11. Thao tác

Thao tác gồm:

1. Xem nhanh
2. Chỉnh sửa
3. Xóa

Badge nên có:

1. Chờ duyệt
2. Đã duyệt
3. Bị từ chối
4. Đã bán
5. Cho thuê
6. Nổi bật

### 10.8 Tạo tin đăng bất động sản

Nên tạo giao diện form nhiều nhóm rõ ràng.

Nhóm thông tin cơ bản:

1. Tiêu đề
2. Mô tả
3. Giá
4. Diện tích
5. Loại bất động sản
6. Trạng thái bất động sản
7. Đánh dấu nổi bật

Nhóm thông tin chi tiết:

1. Số phòng ngủ
2. Số phòng tắm

Nhóm vị trí:

1. Tỉnh thành
2. Quận huyện
3. Địa chỉ có sẵn hoặc chọn `dia_chi_id`
4. Có thể thêm khu vực xem trước bản đồ

Nhóm media:

1. Khu vực tải ảnh hoặc nhập URL ảnh.
2. Gallery preview.

Lưu ý quan trọng:

1. Khi môi giới tạo tin, backend đặt `is_duyet = false`.
2. FE phải hiển thị thông báo rõ: “Tin đăng đã tạo thành công và đang chờ admin duyệt”.

### 10.9 Cập nhật tin đăng

Giao diện gần giống form tạo tin.

Lưu ý:

1. Khi cập nhật tin, backend cũng đưa tin về trạng thái chờ duyệt lại.
2. FE phải hiển thị cảnh báo trước khi lưu: “Sau khi chỉnh sửa, tin sẽ chờ duyệt lại”.

### 10.10 Xóa tin đăng

1. Dùng modal xác nhận.
2. Nêu rõ tiêu đề tin sắp xóa.
3. Sau khi xóa, hiển thị toast thành công.

### 10.11 Thông báo khách hàng thả tim

Tạo một màn hình hoặc dropdown thông báo.

Mỗi thông báo hiển thị:

1. Tên khách hàng.
2. Tên bất động sản.
3. Nội dung thông báo.
4. Thời gian.
5. Trạng thái đã đọc/chưa đọc nếu muốn dựng UI.

Lưu ý:

1. Backend hiện trả về 5 hoạt động mới nhất.
2. Chưa thấy API đánh dấu đã đọc, nên UI chỉ cần read-only.

### 10.12 Gói tin và thanh toán

Tạo một trang mua gói chuyên biệt cho môi giới.

Màn hình gồm:

1. Header giới thiệu quyền lợi gói tin.
2. Card hiển thị gói hiện tại.
3. Card hiển thị số tin còn lại.
4. Card hiển thị ngày hết hạn.
5. Danh sách pricing card của các gói.

Mỗi pricing card hiển thị:

1. Tên gói.
2. Giá tiền.
3. Số ngày hiệu lực.
4. Số lượng tin đăng.
5. Nút “Thanh toán qua SePay”.

Flow thanh toán:

1. Người dùng bấm mua gói.
2. Mở modal xác nhận thông tin gói.
3. Gọi API tạo thanh toán.
4. Nhận `payment_form`.
5. Frontend submit form HTML sang SePay.
6. Sau khi quay về hệ thống, hiển thị trang kết quả thanh toán.

### 10.13 Trang kết quả thanh toán

Trang này cần 3 trạng thái:

1. Thanh toán thành công.
2. Thanh toán thất bại.
3. Đơn đang chờ xác nhận.

Thông tin cần hiển thị:

1. Mã giao dịch.
2. Tên gói.
3. Số tiền.
4. Trạng thái.
5. Nút quay lại trang gói tin.

## 11. Màn hình Admin chi tiết

### 11.1 Đăng nhập admin

Form đơn giản, nghiêm túc:

1. Email
2. Mật khẩu
3. Nút đăng nhập
4. Link quên mật khẩu

### 11.2 Quên mật khẩu admin

Luồng tương tự:

1. Nhập email nhận OTP.
2. Nhập OTP và mật khẩu mới.

### 11.3 Dashboard admin

Dashboard admin là màn hình quan trọng nhất.

Tầng 1 gồm 4 KPI card:

1. Tổng số môi giới
2. Tổng số khách hàng
3. Tổng số bất động sản đã duyệt
4. Tổng số giao dịch thành công

Tầng 2 gồm biểu đồ:

1. Biểu đồ doanh thu theo ngày
2. Biểu đồ số giao dịch theo ngày
3. Bộ lọc thời gian:
4. 7 ngày
5. 30 ngày
6. 3 tháng
7. 6 tháng
8. 1 năm
9. Hoặc khoảng ngày custom

Tầng 3 gồm 2 bảng/card:

1. Khách hàng vừa thả tim gần đây
2. Giao dịch mua gói gần đây

Mỗi block cần có:

1. Tiêu đề block
2. Nút xem thêm
3. Empty state nếu không có dữ liệu

### 11.4 Hồ sơ admin

Form gồm:

1. Tên admin
2. Email
3. Nút cập nhật
4. Nút đăng xuất tất cả thiết bị

### 11.5 Quản lý khách hàng

Màn hình dạng table quản trị.

Bộ lọc đầu trang:

1. Ô tìm kiếm theo tên/email/số điện thoại
2. Dropdown trạng thái active/inactive
3. Nút làm mới

Bảng gồm cột:

1. ID
2. Họ tên
3. Email
4. Số điện thoại
5. Trạng thái kích hoạt
6. Ngày tạo
7. Thao tác

Thao tác:

1. Xem nhanh
2. Chỉnh sửa
3. Xóa

Modal sửa khách hàng gồm:

1. Tên
2. Email
3. Số điện thoại
4. Công tắc active/inactive

### 11.6 Quản lý môi giới

Gần giống quản lý khách hàng nhưng nhiều trường hơn.

Bảng gồm:

1. ID
2. Họ tên
3. Email
4. Số điện thoại
5. Zalo link
6. Mô tả
7. Trạng thái
8. Số tin còn lại
9. Ngày hết hạn gói
10. Thao tác

Modal sửa môi giới gồm:

1. Tên
2. Email
3. Số điện thoại
4. Mô tả
5. Zalo link
6. Công tắc active/inactive

### 11.7 Quản lý bất động sản

Đây là module rất quan trọng.

Trang quản lý bất động sản cần có:

1. Thanh filter đầu trang.
2. Ô tìm kiếm từ khóa.
3. Filter theo loại bất động sản.
4. Filter theo trạng thái.
5. Filter theo tình trạng duyệt.
6. Nút làm mới.

Bảng hoặc grid quản trị cần hiển thị:

1. Ảnh đại diện.
2. Tiêu đề.
3. Giá.
4. Diện tích.
5. Loại bất động sản.
6. Trạng thái bất động sản.
7. Môi giới đăng tin.
8. Tỉnh/Quận.
9. Cờ đã duyệt hay chưa.
10. Cờ nổi bật.
11. Ngày tạo.
12. Thao tác.

Thao tác admin:

1. Xem chi tiết.
2. Duyệt hoặc bỏ duyệt tin.
3. Đổi trạng thái bất động sản.
4. Xóa tin.

### 11.8 Chi tiết bất động sản cho admin

Trang hoặc modal chi tiết cần có:

1. Gallery ảnh.
2. Toàn bộ thông tin bất động sản.
3. Thông tin môi giới đăng.
4. Thông tin địa chỉ đầy đủ.
5. Nút duyệt tin.
6. Nút đổi trạng thái.
7. Nút xóa.

### 11.9 Duyệt tin bất động sản

Thiết kế UX cần rõ ràng:

1. Nút duyệt là toggle.
2. Nếu tin chưa duyệt, nút hiển thị “Duyệt”.
3. Nếu tin đã duyệt, nút hiển thị “Bỏ duyệt”.
4. Dùng badge màu để phân biệt.

### 11.10 Đổi trạng thái bất động sản

Admin có thể đổi trạng thái như:

1. Chưa duyệt
2. Đã duyệt
3. Đã bán
4. Cho thuê
5. Đã hết hạn
6. Bị từ chối

Tạo modal dropdown chọn trạng thái mới rồi xác nhận.

### 11.11 Quản lý loại bất động sản

Màn hình CRUD đơn giản:

1. Danh sách loại bất động sản.
2. Nút thêm loại mới.
3. Nút sửa.
4. Nút xóa.

Form loại bất động sản chỉ cần:

1. Tên loại

### 11.12 Quản lý gói tin

Màn hình CRUD gói tin cần rất rõ.

Bảng hiển thị:

1. ID
2. Tên gói
3. Giá
4. Số ngày
5. Số lượng tin
6. Ngày tạo
7. Thao tác

Form thêm/sửa gói:

1. Tên gói
2. Giá
3. Số ngày hiệu lực
4. Số lượng tin đăng

### 11.13 Lịch sử mua gói

Tạo màn hình bảng lịch sử mua gói:

1. Môi giới
2. Tên gói
3. Ngày bắt đầu
4. Ngày kết thúc
5. Trạng thái còn hiệu lực hay đã hết hạn

### 11.14 Giao dịch

Tạo màn hình giao dịch cho admin với table:

1. Mã giao dịch
2. Môi giới
3. Gói tin
4. Số tiền
5. Phương thức
6. Trạng thái
7. Mã SePay
8. Ngày tạo

Lưu ý:

1. Route giao dịch cho admin đã khai báo.
2. Nhưng trong backend hiện tại chưa thấy method hoàn chỉnh trong controller để cấp dữ liệu.
3. Frontend vẫn nên dựng sẵn giao diện bảng giao dịch.

### 11.15 Chức vụ

Màn hình quản lý chức vụ cho admin:

1. Danh sách chức vụ.
2. Nút thêm mới.
3. Nút sửa.
4. Nút xóa.

Thông tin chức vụ:

1. Tên chức vụ
2. Slug chức vụ

### 11.16 Chức năng

Màn hình danh sách chức năng hệ thống:

1. ID chức năng
2. Tên chức năng
3. URL API
4. Method API
5. Mô tả chức năng

Nên hiển thị read-only dạng table lớn, có tìm kiếm và filter theo method.

### 11.17 Phân quyền

Màn hình phân quyền cần trực quan, dễ thao tác.

Đề xuất giao diện:

1. Cột trái là danh sách chức vụ.
2. Cột phải là danh sách chức năng dạng table hoặc checklist.
3. Mỗi dòng có:
4. Tên chức năng.
5. API URL.
6. Method.
7. Mô tả.
8. Checkbox đã cấp quyền hay chưa.
9. Nút thêm quyền.
10. Nút hủy quyền.

UX mong muốn:

1. Chọn một chức vụ.
2. Nạp danh sách quyền hiện có.
3. Cho phép cấp quyền từng chức năng.
4. Có thể làm thêm thao tác chọn nhiều quyền.

## 12. Thành phần UI dùng lại

Frontend cần xây một bộ component dùng lại thống nhất:

1. `AppNavbar`
2. `AppSidebar`
3. `StatCard`
4. `PropertyCard`
5. `PropertyTable`
6. `FilterBar`
7. `SearchInput`
8. `ConfirmModal`
9. `StatusBadge`
10. `EmptyState`
11. `LoadingSpinner`
12. `ToastMessage`
13. `PricingCard`
14. `ProfileCard`
15. `NotificationList`
16. `MapPanel`

## 13. Quy tắc hiển thị badge và trạng thái

### 13.1 Trạng thái duyệt

1. Chờ duyệt: badge vàng
2. Đã duyệt: badge xanh lá
3. Bị từ chối: badge đỏ

### 13.2 Trạng thái hoạt động tài khoản

1. Active: badge xanh
2. Inactive: badge xám hoặc đỏ nhạt

### 13.3 Trạng thái giao dịch

1. `pending`: badge vàng
2. `success`: badge xanh lá
3. `failed` hoặc `fail`: badge đỏ
4. `cancelled`: badge xám

### 13.4 Trạng thái bất động sản

1. Chưa duyệt
2. Đã duyệt
3. Đã bán
4. Cho thuê
5. Đã hết hạn
6. Bị từ chối

Mỗi trạng thái cần màu riêng, đồng nhất toàn hệ thống.

## 14. Danh sách API nên map vào frontend

### 14.1 Public

1. `GET /api/bat-dong-san`
2. `GET /api/bat-dong-san/{id}`
3. `POST /api/tim-kiem`
4. `GET /api/tinh-thanh`
5. `GET /api/quan-huyen`

### 14.2 Auth Admin

1. `POST /api/admin/dang-nhap`
2. `GET /api/admin/dang-xuat`
3. `GET /api/admin/check-token`
4. `POST /api/admin/forgot-password/send-otp`
5. `POST /api/admin/forgot-password/reset`

### 14.3 Auth Khách Hàng

1. `POST /api/khach-hang/dang-nhap`
2. `POST /api/khach-hang/register`
3. `GET /api/khach-hang/check-token`
4. `POST /api/khach-hang/forgot-password/send-otp`
5. `POST /api/khach-hang/forgot-password/reset`
6. `GET /api/khach-hang/profile`
7. `POST /api/khach-hang/update-profile`
8. `POST /api/khach-hang/update-password`
9. `POST /api/khach-hang/logout`

### 14.4 Khách Hàng

1. `GET /api/khach-hang/dia-chi`
2. `GET /api/khach-hang/dia-chi/{id}`
3. `GET /api/khach-hang/bds-khu-vuc`
4. `GET /api/khach-hang/map/bat-dong-san`
5. `GET /api/khach-hang/map/nearby`
6. `POST /api/khach-hang/bds/yeu-thich`
7. `GET /api/khach-hang/bds/yeu-thich/data`
8. `POST /api/khach-hang/mua-goi`

### 14.5 Auth Môi Giới

1. `POST /api/moi-gioi/dang-nhap`
2. `POST /api/moi-gioi/dang-ky`
3. `GET /api/moi-gioi/check-token`
4. `POST /api/moi-gioi/forgot-password/send-otp`
5. `POST /api/moi-gioi/forgot-password/reset`
6. `GET /api/moi-gioi/profile`
7. `POST /api/moi-gioi/update-profile`
8. `POST /api/moi-gioi/update-password`
9. `POST /api/moi-gioi/logout`

### 14.6 Môi Giới

1. `GET /api/moi-gioi/bds/data`
2. `POST /api/moi-gioi/bds/create`
3. `POST /api/moi-gioi/bds/update`
4. `POST /api/moi-gioi/bds/delete`
5. `GET /api/moi-gioi/goi-tin/data`
6. `POST /api/moi-gioi/goi-tin/mua`
7. `GET /api/moi-gioi/thong-bao`
8. `POST /api/moi-gioi/payment/create`
9. `POST /api/payment/sepay-webhook`
10. `ANY /api/payment/sepay-return`

### 14.7 Admin

1. `GET /api/admin/profile`
2. `POST /api/admin/update-profile`
3. `GET /api/admin/dang-xuat-tat-ca`
4. `GET /api/admin/khach-hang/data`
5. `POST /api/admin/khach-hang/search`
6. `POST /api/admin/khach-hang/update`
7. `POST /api/admin/khach-hang/delete`
8. `GET /api/admin/moi-gioi/data`
9. `POST /api/admin/moi-gioi/search`
10. `POST /api/admin/moi-gioi/update`
11. `POST /api/admin/moi-gioi/delete`
12. `GET /api/admin/bds/data`
13. `GET /api/admin/bds/{id}`
14. `POST /api/admin/bds/duyet`
15. `POST /api/admin/bds/delete`
16. `POST /api/admin/bds/change-status`
17. `POST /api/admin/bds/tim-kiem`
18. `GET /api/admin/loai-bds/data`
19. `POST /api/admin/loai-bds/create`
20. `PUT /api/admin/loai-bds/update/{id}`
21. `DELETE /api/admin/loai-bds/delete/{id}`
22. `GET /api/admin/goi-tin/data`
23. `POST /api/admin/goi-tin/create`
24. `PUT /api/admin/goi-tin/update`
25. `DELETE /api/admin/goi-tin/delete/{id}`
26. `GET /api/admin/goi-tin/lich-su-mua`
27. `GET /api/admin/dashboard/stats`
28. `POST /api/admin/dashboard/revenue-chart`
29. `GET /api/admin/dashboard/recent-favorites`
30. `GET /api/admin/dashboard/recent-package-purchases`
31. `GET /api/admin/giao-dich/data`
32. `GET /api/admin/chuc-vu/data`
33. `POST /api/admin/chuc-vu/create`
34. `POST /api/admin/chuc-vu/update`
35. `POST /api/admin/chuc-vu/delete`
36. `GET /api/admin/chuc-nang/data`
37. `GET /api/admin/phan-quyen/data/{id_chuc_vu}`
38. `POST /api/admin/phan-quyen/chuc-vu/create`
39. `POST /api/admin/phan-quyen/chuc-vu/delete`

## 15. Ghi chú quan trọng khi sinh FE

### 15.1 Những điểm backend đã có rõ

1. Public xem danh sách và chi tiết bất động sản.
2. Có cơ chế ẩn thông tin liên hệ cho khách chưa đăng nhập.
3. Khách hàng có thể thả tim và xem danh sách yêu thích.
4. Khách hàng có module địa chỉ và bản đồ.
5. Môi giới có CRUD bất động sản của mình.
6. Môi giới có thông báo khách thả tim.
7. Admin có dashboard thống kê khá đầy đủ.
8. Admin có quản lý khách hàng, môi giới, bất động sản, loại bất động sản, gói tin, chức vụ, phân quyền.
9. Thanh toán SePay đã có flow tạo form và callback.

### 15.2 Những điểm backend còn dang dở hoặc cần FE thiết kế dự phòng

1. Một số route mua gói và lấy danh sách gói cho môi giới/khách hàng đã khai báo nhưng controller hiện tại chưa hoàn thiện đầy đủ.
2. Route giao dịch admin đã có nhưng controller hiện tại chưa thấy method dữ liệu hoàn chỉnh.
3. API AI định giá hiện mới là placeholder.
4. API chatbot hiện mới là placeholder.
5. Phần ảnh bất động sản có model và dữ liệu trả ra ở chi tiết, nhưng luồng upload/quản lý ảnh cho môi giới chưa thấy hoàn chỉnh trong controller chính.

Vì vậy, khi sinh FE:

1. Vẫn tạo đầy đủ UI cho các module trên.
2. Tách rõ phần đã sẵn sàng tích hợp API và phần có thể dùng mock data tạm.
3. Ưu tiên dựng kiến trúc component tốt để sau này cắm API thật dễ dàng.

## 16. Yêu cầu UX cuối cùng

Frontend cần đem lại cảm giác:

1. Dễ dùng với người phổ thông khi tìm nhà đất.
2. Hiệu quả với môi giới khi quản lý tin đăng.
3. Rõ ràng và mạnh về dữ liệu với admin.

Ưu tiên:

1. Responsive tốt.
2. Form rõ ràng.
3. Table quản trị dễ nhìn.
4. Badge trạng thái nổi bật.
5. Giá bất động sản hiển thị đẹp.
6. Bản đồ dễ thao tác.
7. Các luồng thanh toán, duyệt tin, xóa dữ liệu có xác nhận rõ ràng.

## 17. Kết luận ngắn để AI sinh FE hiểu đúng

Hãy tạo một hệ thống frontend bất động sản hoàn chỉnh bằng **Bootstrap 5**, gồm:

1. Website public xem và tìm kiếm bất động sản.
2. Khu vực khách hàng với hồ sơ, yêu thích, bản đồ, tìm kiếm khu vực.
3. Khu vực môi giới với dashboard, quản lý tin đăng, hồ sơ, thông báo, mua gói và thanh toán SePay.
4. Khu vực admin với dashboard thống kê, quản lý người dùng, quản lý bất động sản, duyệt tin, quản lý gói tin, loại bất động sản, chức vụ và phân quyền.

Toàn bộ giao diện phải nhất quán, hiện đại vừa đủ, thiên về doanh nghiệp, dễ dùng, rõ dữ liệu và sẵn sàng tích hợp API Laravel backend hiện tại.
