# Prompt tạo Frontend Bootstrap cho hệ thống Bất Động Sản

Hãy tạo một hệ thống frontend web responsive bằng Bootstrap 5 cho một nền tảng bất động sản. Hệ thống có 2 khu vực lớn:

1. Admin Portal
2. Client Portal

Trong Client Portal có 3 trạng thái người dùng:

1. Khách vãng lai chưa đăng nhập
2. Khách hàng đã đăng nhập
3. Môi giới đã đăng nhập

Phong cách giao diện cần hiện đại, chuyên nghiệp, dễ tin cậy, thiên về lĩnh vực bất động sản. Ưu tiên tông màu xanh navy, trắng, xám sáng, điểm nhấn vàng nhạt hoặc xanh ngọc cho CTA. Toàn bộ UI dùng Bootstrap 5, Bootstrap Icons, card, table, modal, offcanvas, dropdown, toast, pagination, tabs, accordion. Không dùng phong cách quá màu mè. Phải responsive tốt trên desktop, tablet và mobile.

## 1. Tổng quan hệ thống

Đây là hệ thống quản lý và đăng tin bất động sản. Backend hiện tại là Laravel API với các nhóm chức năng chính:

1. Quản lý đăng nhập riêng cho Admin, Khách hàng, Môi giới
2. Danh sách bất động sản public cho người dùng ngoài hệ thống
3. Tìm kiếm bất động sản theo tỉnh, loại, giá, tiêu đề
4. Xem chi tiết bất động sản
5. Xem môi giới phụ trách bất động sản
6. Hiển thị bất động sản trên bản đồ theo tọa độ địa chỉ
7. Khách hàng yêu thích bất động sản
8. Môi giới đăng tin, sửa tin, xóa tin, theo dõi thông báo khi khách hàng thả tim
9. Môi giới mua gói đăng tin
10. Admin duyệt tin, đổi trạng thái tin, quản lý user, quản lý gói tin, thống kê, giao dịch, vai trò, phân quyền
11. Có module AI định giá bất động sản
12. Có module chatbot tư vấn bất động sản

Lưu ý quan trọng để thiết kế FE:

1. Bất động sản public chỉ hiển thị khi `is_duyet = true`
2. Bất động sản có 2 lớp trạng thái:
   - Trạng thái kiểm duyệt: `is_duyet`
   - Trạng thái kinh doanh: `trang_thai_id`
3. Khi môi giới sửa tin, backend đang reset lại `is_duyet = false`, vì vậy FE phải hiển thị rõ trạng thái "chờ duyệt lại"
4. Tính năng yêu thích là dạng toggle thích hoặc bỏ thích
5. Môi giới nhận thông báo khi khách hàng thả tim vào tin của mình
6. Mua gói hiện tại backend đang xử lý thanh toán thành công ngay sau khi chọn gói

## 2. Yêu cầu UI/UX chung

Tạo 2 shell giao diện tách biệt:

1. `Admin Portal`
   - Có sidebar trái cố định trên desktop
   - Header trên cùng có avatar admin, dropdown profile, logout
   - Nội dung chính dạng dashboard app
   - Dùng table cho các màn quản trị

2. `Client Portal`
   - Có top navbar public
   - Có home page theo kiểu landing page kết hợp listing
   - Có trang danh sách, trang chi tiết, trang bản đồ
   - Có dashboard mini cho khách hàng và dashboard riêng cho môi giới

Yêu cầu responsive:

1. Mobile: sidebar chuyển thành offcanvas
2. Table trên mobile có thể scroll ngang hoặc chuyển thành card list
3. Bộ lọc listing trên mobile dùng offcanvas filter
4. Form dài phải chia section rõ ràng
5. Nút chính phải rõ, to, dễ bấm

Component nên có:

1. `Navbar`
2. `Sidebar`
3. `Page header`
4. `Stats cards`
5. `Data table`
6. `Search bar`
7. `Filter panel`
8. `Property card`
9. `Broker card`
10. `Gallery carousel`
11. `Map section`
12. `Notification dropdown`
13. `Modal confirm`
14. `Toast success/error`
15. `Empty state`
16. `Skeleton loading`

## 3. Dữ liệu chính cần phản ánh trên giao diện

### 3.1. Bất động sản

Các field chính:

1. `id`
2. `tieu_de`
3. `mo_ta`
4. `gia`
5. `dien_tich`
6. `loai_id`
7. `trang_thai_id`
8. `moi_gioi_id`
9. `tinh_id`
10. `quan_id`
11. `dia_chi_id`
12. `so_phong_ngu`
13. `so_phong_tam`
14. `is_duyet`
15. `is_noi_bat`
16. danh sách `hinhAnh`
17. địa chỉ chi tiết qua `diaChi`
18. môi giới phụ trách qua `moiGioi`

### 3.2. Loại bất động sản

Ví dụ seed data hiện có:

1. Căn hộ
2. Nhà phố
3. Nhà riêng
4. Đất nền
5. Kho xưởng
6. Văn phòng
7. Cửa hàng
8. Trang trại

### 3.3. Trạng thái bất động sản

Ví dụ seed data hiện có:

1. Chưa duyệt
2. Đã duyệt
3. Đã bán
4. Cho thuê
5. Đã hết hạn
6. Bị từ chối

FE phải hiển thị trạng thái bằng badge màu:

1. `Chờ duyệt`: warning
2. `Đã duyệt`: success
3. `Đã bán`: secondary hoặc dark
4. `Cho thuê`: info
5. `Hết hạn`: danger
6. `Bị từ chối`: danger
7. `Nổi bật`: badge riêng màu vàng

### 3.4. Người dùng

Có 3 loại user:

1. Admin
2. Khách hàng
3. Môi giới

Khách hàng và môi giới có các thông tin:

1. `ten`
2. `email`
3. `so_dien_thoai`
4. `is_active`

Môi giới có thêm:

1. `avatar`
2. `mo_ta`
3. `zalo_link`

### 3.5. Gói tin

Field chính:

1. `ten_goi`
2. `gia`
3. `so_ngay`
4. `so_luong_tin`

### 3.6. Giao dịch

Field chính:

1. `moi_gioi_id`
2. `goi_tin_id`
3. `so_tien`
4. `phuong_thuc`
5. `trang_thai`
6. `ma_giao_dich`
7. `created_at`

### 3.7. Yêu thích và thông báo

Yêu thích lưu:

1. môi giới nhận thông báo
2. khách hàng thực hiện hành động
3. bất động sản liên quan
4. nội dung thông báo
5. trạng thái đã đọc hoặc chưa đọc

## 4. Kiến trúc trang và điều hướng

## 4.1. Admin Portal

Menu sidebar đề xuất:

1. Dashboard
2. Quản lý khách hàng
3. Quản lý môi giới
4. Quản lý bất động sản
5. Quản lý gói tin
6. Lịch sử mua gói
7. Quản lý giao dịch
8. Quản lý chức vụ
9. Quản lý phân quyền
10. Hồ sơ admin

## 4.2. Client Portal

Menu navbar đề xuất:

1. Trang chủ
2. Danh sách bất động sản
3. Bản đồ bất động sản
4. AI định giá
5. Chatbot tư vấn
6. Đăng nhập
7. Đăng ký

Khi đăng nhập khách hàng:

1. Tài khoản của tôi
2. Danh sách yêu thích
3. Cập nhật hồ sơ
4. Đổi mật khẩu
5. Nâng cấp gói môi giới

Khi đăng nhập môi giới:

1. Dashboard môi giới
2. Tin của tôi
3. Đăng tin mới
4. Gói tin
5. Giao dịch
6. Thông báo
7. Hồ sơ môi giới

## 5. Đặc tả chi tiết từng màn hình Admin

### 5.1. Trang đăng nhập Admin

Tạo trang đăng nhập riêng cho admin tại route kiểu `/admin/login`.

Thiết kế:

1. Layout chia 2 cột trên desktop
2. Cột trái là form đăng nhập
3. Cột phải là ảnh minh họa hoặc block branding của hệ thống quản trị BĐS
4. Form card bo góc, shadow nhẹ
5. Có logo, tiêu đề "Admin Bất Động Sản"

Field:

1. Email
2. Mật khẩu
3. Checkbox ghi nhớ đăng nhập nếu muốn
4. Nút đăng nhập
5. Link quên mật khẩu

Flow quên mật khẩu:

1. Bước nhập email
2. Bước nhập OTP
3. Bước nhập mật khẩu mới

### 5.2. Dashboard Admin

Đây là màn landing sau khi admin đăng nhập.

Hiển thị:

1. 4 stats card:
   - Tổng môi giới
   - Tổng khách hàng
   - Tổng bất động sản đã duyệt
   - Tổng giao dịch thành công
2. 1 card doanh thu tổng
3. 1 biểu đồ doanh thu theo ngày
4. Bộ lọc thời gian:
   - Từ ngày
   - Đến ngày
5. Khu vực cảnh báo:
   - Tin chờ duyệt
   - User bị khóa
   - Giao dịch gần đây

Style:

1. Dùng card Bootstrap
2. Có icon rõ cho từng stats
3. Biểu đồ vùng hoặc cột

### 5.3. Quản lý khách hàng

Màn hình dạng table quản trị.

Tính năng:

1. Xem danh sách khách hàng
2. Tìm kiếm theo tên hoặc email
3. Phân trang
4. Chỉnh sửa khách hàng
5. Khóa hoặc mở khóa tài khoản qua `is_active`
6. Xóa khách hàng

Table columns:

1. ID
2. Tên
3. Email
4. Số điện thoại
5. Trạng thái hoạt động
6. Ngày tạo
7. Hành động

Action UI:

1. Nút sửa mở modal hoặc drawer
2. Nút khóa hoặc mở khóa đổi badge và switch
3. Nút xóa có modal xác nhận

Form sửa:

1. Tên
2. Số điện thoại
3. Trạng thái hoạt động

### 5.4. Quản lý môi giới

Màn này tương tự quản lý khách hàng nhưng có thêm thông tin nghề nghiệp.

Tính năng:

1. Xem danh sách môi giới
2. Tìm kiếm theo tên, email, số điện thoại
3. Cập nhật thông tin
4. Bật hoặc tắt tài khoản
5. Xóa môi giới

Table columns:

1. ID
2. Tên
3. Email
4. Số điện thoại
5. Zalo link
6. Mô tả ngắn
7. Trạng thái hoạt động
8. Số tin đang có nếu muốn hiển thị thêm
9. Hành động

Chi tiết môi giới có thể mở drawer:

1. Avatar
2. Mô tả
3. Link Zalo
4. Danh sách tin đã đăng
5. Lịch sử giao dịch mua gói

### 5.5. Quản lý bất động sản

Đây là màn quan trọng nhất của admin.

Tính năng:

1. Xem toàn bộ tin bất động sản
2. Lọc theo môi giới
3. Tìm kiếm theo tiêu đề
4. Lọc theo tỉnh
5. Lọc theo loại bất động sản
6. Lọc theo giá tối thiểu
7. Duyệt tin
8. Đổi trạng thái kinh doanh của tin
9. Xóa tin
10. Xem chi tiết tin

Table columns:

1. ID
2. Ảnh đại diện
3. Tiêu đề
4. Loại bất động sản
5. Giá
6. Diện tích
7. Tỉnh hoặc quận
8. Môi giới
9. Trạng thái kiểm duyệt
10. Trạng thái kinh doanh
11. Nổi bật hay không
12. Ngày tạo
13. Hành động

Trong mỗi row nên có:

1. Badge `Chờ duyệt` hoặc `Đã duyệt`
2. Badge trạng thái `Đã bán`, `Cho thuê`, `Hết hạn`
3. Nút `Xem`
4. Nút `Duyệt`
5. Nút `Đổi trạng thái`
6. Nút `Xóa`

Trang chi tiết tin trong admin hoặc drawer lớn phải hiển thị:

1. Gallery ảnh
2. Tiêu đề
3. Mô tả
4. Giá
5. Diện tích
6. Số phòng ngủ
7. Số phòng tắm
8. Loại BĐS
9. Trạng thái
10. Địa chỉ đầy đủ
11. Vị trí trên bản đồ
12. Thông tin môi giới
13. Trạng thái duyệt

Modal đổi trạng thái:

1. Select trạng thái
2. Nút cập nhật

### 5.6. Quản lý gói tin

Màn CRUD gói đăng tin.

Tính năng:

1. Xem danh sách gói
2. Thêm gói mới
3. Cập nhật gói
4. Xóa gói

Table columns:

1. ID
2. Tên gói
3. Giá
4. Số ngày
5. Số lượng tin
6. Ngày tạo
7. Hành động

Form thêm hoặc sửa:

1. Tên gói
2. Giá
3. Số ngày hiệu lực
4. Số lượng tin được đăng

Hiển thị gói theo card song song ở phần preview:

1. Gói Cơ Bản
2. Gói Tiêu Chuẩn
3. Gói VIP
4. Gói Cao Cấp

### 5.7. Lịch sử mua gói

Màn admin xem lịch sử môi giới đã mua gói nào.

Tính năng:

1. Danh sách lịch sử mua gói
2. Phân trang
3. Xem môi giới
4. Xem gói
5. Xem ngày bắt đầu và ngày kết thúc

Table columns:

1. ID
2. Môi giới
3. Gói tin
4. Ngày bắt đầu
5. Ngày kết thúc
6. Số ngày còn lại nếu muốn
7. Trạng thái còn hiệu lực hoặc hết hạn

### 5.8. Quản lý giao dịch

Màn này tập trung vào doanh thu và payment logs.

Tính năng:

1. Xem danh sách giao dịch
2. Tìm kiếm theo mã giao dịch hoặc tên môi giới
3. Xem doanh thu tổng
4. Xem số giao dịch thành công
5. Xem số giao dịch hôm nay
6. Phân trang

Table columns:

1. ID
2. Mã giao dịch
3. Môi giới
4. Gói tin
5. Số tiền
6. Phương thức thanh toán
7. Trạng thái
8. Thời gian tạo

Các stats ở đầu trang:

1. Tổng doanh thu
2. Tổng giao dịch thành công
3. Giao dịch hôm nay

### 5.9. Quản lý chức vụ

Màn dành cho quản trị vai trò admin.

Tính năng:

1. Xem danh sách chức vụ
2. Thêm chức vụ
3. Sửa chức vụ
4. Xóa chức vụ

Table columns:

1. ID
2. Tên chức vụ
3. Slug chức vụ
4. Hành động

Ví dụ chức vụ:

1. Nhân viên kiểm duyệt
2. Admin

### 5.10. Quản lý phân quyền

Màn phân quyền theo chức vụ.

Thiết kế đề xuất:

1. Cột trái là danh sách chức vụ
2. Cột phải là danh sách chức năng có checkbox hoặc switch
3. Có nút cấp quyền
4. Có nút hủy quyền

Mỗi chức năng hiển thị:

1. Tên chức năng
2. URL API
3. Method
4. Mô tả chức năng
5. Trạng thái đã cấp hay chưa

Nếu là super admin:

1. Thấy toàn bộ chức năng
2. Có thể chỉnh phân quyền mọi role

### 5.11. Hồ sơ Admin

Màn profile cá nhân:

1. Tên
2. Email
3. Cập nhật hồ sơ
4. Đăng xuất
5. Đăng xuất tất cả thiết bị

## 6. Đặc tả chi tiết từng màn hình Client

Client Portal phải hiểu theo nghĩa rộng: toàn bộ giao diện dành cho người dùng không phải admin, bao gồm khách vãng lai, khách hàng và môi giới.

### 6.1. Trang chủ public

Trang chủ phải mang cảm giác website bất động sản chuyên nghiệp.

Các khối chính:

1. Hero banner lớn với headline rõ:
   - "Tìm bất động sản phù hợp cho bạn"
2. Form tìm kiếm nhanh ngay trong hero:
   - Tỉnh thành
   - Loại BĐS
   - Giá từ
   - Giá đến
   - Từ khóa tiêu đề
   - Nút tìm kiếm
3. Section bất động sản nổi bật
4. Section bất động sản mới nhất
5. Section tìm theo loại bất động sản
6. Section tìm theo khu vực
7. Khối giới thiệu về nền tảng
8. CTA đăng ký làm môi giới
9. CTA dùng AI định giá
10. Khối chatbot trợ lý nổi dạng floating button

Property card trên homepage cần có:

1. Ảnh chính
2. Badge loại
3. Badge nổi bật nếu có
4. Tiêu đề
5. Giá
6. Diện tích
7. Số phòng ngủ
8. Số phòng tắm
9. Địa chỉ ngắn
10. Tên môi giới
11. Nút xem chi tiết
12. Nút yêu thích nếu user là khách hàng đã đăng nhập

### 6.2. Trang danh sách bất động sản

Đây là trang listing chính.

Layout:

1. Cột trái là filter panel trên desktop
2. Cột phải là grid danh sách bất động sản
3. Mobile dùng offcanvas filter

Bộ lọc:

1. Từ khóa tiêu đề
2. Tỉnh
3. Loại bất động sản
4. Giá tối thiểu
5. Giá tối đa

Tính năng:

1. Tìm kiếm
2. Sắp xếp theo giá mới nhất hoặc nổi bật nếu muốn thêm UI
3. Chuyển chế độ grid hoặc list
4. Phân trang
5. Empty state khi không có dữ liệu

Mỗi item:

1. Ảnh
2. Tiêu đề
3. Giá
4. Diện tích
5. Loại
6. Môi giới
7. Nút xem chi tiết
8. Nút yêu thích

### 6.3. Trang chi tiết bất động sản

Đây là trang conversion chính.

Bố cục:

1. Bên trái là gallery ảnh lớn
2. Bên phải là summary card sticky
3. Bên dưới là mô tả, thông số, bản đồ, thông tin môi giới

Thông tin phải có:

1. Tiêu đề
2. Giá
3. Diện tích
4. Loại bất động sản
5. Trạng thái kinh doanh
6. Số phòng ngủ
7. Số phòng tắm
8. Địa chỉ chi tiết
9. Mô tả đầy đủ
10. Gallery nhiều ảnh
11. Thông tin môi giới:
   - Tên
   - Số điện thoại
   - Mô tả
   - Link Zalo
12. Bản đồ vị trí lấy từ `diaChi.lat/lng`
13. Nút yêu thích
14. Nút liên hệ môi giới
15. Nút sao chép link

Nên có thêm section:

1. Tin cùng loại
2. Tin cùng khu vực

### 6.4. Trang bản đồ bất động sản

Trang này dùng API map-data.

Layout đề xuất:

1. Bên trái là danh sách kết quả thu gọn
2. Bên phải là map lớn
3. Trên cùng có filter ngắn

Marker popup cần hiển thị:

1. Ảnh đại diện
2. Giá
3. Diện tích
4. Địa chỉ ngắn
5. Nút xem chi tiết

### 6.5. Trang AI định giá

Đây là trang tính năng nổi bật, nên thiết kế như một công cụ riêng.

Mục tiêu:

1. Cho user nhập thông tin cơ bản
2. Trả kết quả giá dự đoán
3. Hiển thị dưới dạng card kết quả rõ ràng

Form nhập:

1. Loại bất động sản
2. Diện tích
3. Tỉnh thành

Kết quả:

1. Giá dự đoán
2. Ghi chú AI
3. Badge "Beta" hoặc "Ước tính"
4. CTA xem các tin có giá tương đương

Thiết kế:

1. Form card lớn ở giữa
2. Result card nổi bật phía dưới
3. Có background minh họa data, chart hoặc icon AI

### 6.6. Chatbot tư vấn bất động sản

Thiết kế trang chat giống trợ lý tư vấn.

Bố cục:

1. Sidebar trái là gợi ý câu hỏi nhanh
2. Khung chat chính ở giữa
3. Ô nhập message phía dưới

Quick prompt:

1. Tìm căn hộ tại TP.HCM
2. Tư vấn đất nền giá 2 tỷ
3. Nhà phố cho thuê
4. Nên mua hay thuê

Message bubble:

1. Tin nhắn user bên phải
2. Tin nhắn bot bên trái
3. Timestamp nhỏ

Lưu ý:

1. Vì backend chatbot đang là placeholder, FE nên hiển thị nhãn "AI Assistant Beta"

### 6.7. Đăng nhập, đăng ký, quên mật khẩu Client

Không gộp Admin vào đây. Đây là auth cho khách hàng và môi giới.

Tạo 1 trang auth dùng tabs hoặc segmented control:

1. Tab `Khách hàng`
2. Tab `Môi giới`

Các flow cần có:

1. Đăng nhập khách hàng
2. Đăng ký khách hàng
3. Quên mật khẩu khách hàng
4. Đăng nhập môi giới
5. Đăng ký môi giới
6. Quên mật khẩu môi giới

Form khách hàng đăng ký:

1. Tên
2. Email
3. Số điện thoại
4. Mật khẩu
5. Xác nhận mật khẩu

Form môi giới đăng ký:

1. Tên
2. Email
3. Số điện thoại
4. Mật khẩu
5. Xác nhận mật khẩu
6. Link Zalo
7. Mô tả bản thân

Quên mật khẩu:

1. Nhập email
2. Nhập OTP
3. Nhập mật khẩu mới

### 6.8. Khu vực Khách hàng đã đăng nhập

#### 6.8.1. Tài khoản của tôi

Trang tài khoản khách hàng có layout 2 cột:

1. Cột trái là menu tài khoản
2. Cột phải là nội dung

Menu:

1. Thông tin cá nhân
2. Bất động sản yêu thích
3. Đổi mật khẩu
4. Nâng cấp gói môi giới

Block thông tin cá nhân:

1. Tên
2. Email
3. Số điện thoại
4. Nút cập nhật

#### 6.8.2. Danh sách yêu thích

Tính năng:

1. Xem các bất động sản đã thích
2. Bỏ thích
3. Chuyển đến trang chi tiết
4. Phân trang

Hiển thị theo dạng card hoặc table card:

1. Ảnh
2. Tiêu đề
3. Giá
4. Diện tích
5. Tên môi giới
6. Nút bỏ thích
7. Nút xem chi tiết

#### 6.8.3. Đổi mật khẩu

Form:

1. Mật khẩu cũ
2. Mật khẩu mới
3. Xác nhận mật khẩu mới

#### 6.8.4. Nâng cấp gói môi giới

Thiết kế như trang pricing.

Mục tiêu UI:

1. Hiển thị các gói tin thành dạng card pricing
2. Có ribbon `Phổ biến` hoặc `VIP`
3. Chọn phương thức thanh toán
4. Nút mua gói

Form mua:

1. Chọn gói
2. Chọn phương thức `cash`, `bank`, `credit_card`

Lưu ý nghiệp vụ:

1. Backend có route khách hàng mua gói với ý định trở thành môi giới
2. FE nên đặt tên dễ hiểu như `Nâng cấp tài khoản môi giới`

### 6.9. Khu vực Môi giới đã đăng nhập

Môi giới là một phần của Client Portal nhưng cần có dashboard riêng.

#### 6.9.1. Dashboard môi giới

Đây là trang tổng quan sau khi môi giới đăng nhập.

Hiển thị:

1. Số tin đang có
2. Số tin chờ duyệt
3. Số tin đã duyệt
4. Số lượt khách hàng quan tâm gần đây
5. Số giao dịch mua gói
6. Gói đang sử dụng hoặc CTA mua gói

Các block chính:

1. Stats cards
2. Danh sách 5 thông báo mới nhất
3. Danh sách tin gần đây
4. CTA đăng tin mới

#### 6.9.2. Tin của tôi

Màn danh sách bất động sản môi giới đã đăng.

Tính năng:

1. Xem danh sách tin
2. Tạo tin mới
3. Sửa tin
4. Xóa tin
5. Xem trạng thái duyệt
6. Xem trạng thái kinh doanh

Table columns:

1. ID
2. Ảnh
3. Tiêu đề
4. Giá
5. Diện tích
6. Loại
7. Trạng thái duyệt
8. Trạng thái kinh doanh
9. Nổi bật
10. Ngày tạo
11. Hành động

Badge nên có:

1. Chờ duyệt
2. Đã duyệt
3. Chờ duyệt lại sau chỉnh sửa
4. Đã bán
5. Cho thuê
6. Hết hạn

#### 6.9.3. Đăng tin mới và chỉnh sửa tin

Form đăng tin phải rất rõ vì đây là màn nhập dữ liệu quan trọng.

Chia thành các section:

1. Thông tin cơ bản
2. Thông tin bất động sản
3. Vị trí
4. Hình ảnh
5. Xác nhận đăng tin

Field form:

1. Tiêu đề
2. Mô tả
3. Giá
4. Diện tích
5. Loại bất động sản
6. Trạng thái kinh doanh
7. Tỉnh thành
8. Quận huyện
9. Địa chỉ chi tiết hoặc chọn địa chỉ có sẵn
10. Số phòng ngủ
11. Số phòng tắm
12. Checkbox `Tin nổi bật`
13. Khu upload ảnh hoặc nhập URL ảnh

Gợi ý UI:

1. Input group cho giá và diện tích
2. Select phụ thuộc tỉnh và quận
3. Gallery preview ảnh
4. Sticky action bar với nút lưu
5. Alert nhỏ nhắc rằng sau khi tạo hoặc sửa tin sẽ cần admin duyệt

#### 6.9.4. Gói tin

Trang hiển thị các gói môi giới có thể mua.

Layout:

1. Grid 3 hoặc 4 card
2. Mỗi card có:
   - Tên gói
   - Giá
   - Số ngày hiệu lực
   - Số lượng tin
   - Nút chọn gói

Khi chọn gói:

1. Mở modal xác nhận
2. Chọn phương thức thanh toán
3. Xác nhận thanh toán
4. Hiển thị modal thành công

#### 6.9.5. Thông báo quan tâm

Trang hoặc dropdown thông báo cho môi giới.

Hiển thị:

1. 5 hoặc nhiều hơn thông báo khách hàng thả tim
2. Tên khách hàng
3. Tên bất động sản
4. Thời gian
5. Trạng thái đã đọc hoặc chưa đọc

UI đề xuất:

1. Notification dropdown ở header
2. Trang full list dạng timeline hoặc list-group

#### 6.9.6. Giao dịch

Màn xem lịch sử thanh toán của môi giới.

Hiển thị:

1. Mã giao dịch
2. Gói đã mua
3. Số tiền
4. Phương thức thanh toán
5. Trạng thái
6. Ngày tạo

Nếu không có API hoàn chỉnh thì vẫn tạo UI sẵn theo cấu trúc admin giao dịch nhưng lọc theo môi giới hiện tại.

#### 6.9.7. Hồ sơ môi giới

Trang profile môi giới gồm:

1. Tên
2. Email
3. Số điện thoại
4. Link Zalo
5. Mô tả
6. Avatar placeholder
7. Nút cập nhật hồ sơ
8. Nút đổi mật khẩu
9. Nút đăng xuất

## 7. Thiết kế Bootstrap chi tiết

### 7.1. Style guide đề xuất

1. Font dễ đọc, hiện đại
2. Border radius vừa phải
3. Shadow nhẹ cho card
4. Khoảng trắng thoáng
5. Dùng `container-fluid` cho dashboard admin
6. Dùng `container` cho trang public

### 7.2. Màu sắc đề xuất

1. Primary: xanh navy
2. Secondary: xám đậm
3. Success: xanh lá
4. Warning: vàng cam
5. Danger: đỏ
6. Light background: xám rất nhạt

### 7.3. Thành phần Bootstrap nên dùng

1. `navbar`
2. `offcanvas`
3. `dropdown`
4. `card`
5. `table`
6. `badge`
7. `modal`
8. `toast`
9. `accordion`
10. `pagination`
11. `nav-pills`
12. `form-floating`
13. `input-group`
14. `list-group`
15. `breadcrumb`

## 8. Đề xuất route frontend

### 8.1. Admin

1. `/admin/login`
2. `/admin/dashboard`
3. `/admin/khach-hang`
4. `/admin/moi-gioi`
5. `/admin/bat-dong-san`
6. `/admin/goi-tin`
7. `/admin/lich-su-mua-goi`
8. `/admin/giao-dich`
9. `/admin/chuc-vu`
10. `/admin/phan-quyen`
11. `/admin/profile`

### 8.2. Client public

1. `/`
2. `/bat-dong-san`
3. `/bat-dong-san/:id`
4. `/ban-do`
5. `/ai-dinh-gia`
6. `/chatbot`
7. `/dang-nhap`
8. `/dang-ky`

### 8.3. Khách hàng

1. `/tai-khoan`
2. `/tai-khoan/yeu-thich`
3. `/tai-khoan/doi-mat-khau`
4. `/tai-khoan/nang-cap-moi-gioi`

### 8.4. Môi giới

1. `/moi-gioi/dashboard`
2. `/moi-gioi/tin-cua-toi`
3. `/moi-gioi/dang-tin`
4. `/moi-gioi/chinh-sua/:id`
5. `/moi-gioi/goi-tin`
6. `/moi-gioi/giao-dich`
7. `/moi-gioi/thong-bao`
8. `/moi-gioi/profile`

## 9. Mapping API chính để FE tích hợp

### 9.1. Public

1. `GET /api/tinh-thanh`
2. `GET /api/quan-huyen?tinh_id=...`
3. `GET /api/loai-bds`
4. `GET /api/bds`
5. `POST /api/bds/tim-kiem`
6. `GET /api/bds/{id}`
7. `GET /api/bds/{id}/moi-gioi`
8. `GET /api/bds/map-data`
9. `POST /api/ai/dinh-gia`
10. `POST /api/chatbot`

### 9.2. Admin

1. `POST /api/admin/dang-nhap`
2. `GET /api/admin/profile`
3. `POST /api/admin/update-profile`
4. `GET /api/admin/dang-xuat`
5. `GET /api/admin/dang-xuat-tat-ca`
6. `GET /api/admin/khach-hang/data`
7. `POST /api/admin/khach-hang/search`
8. `POST /api/admin/khach-hang/update`
9. `POST /api/admin/khach-hang/delete`
10. `GET /api/admin/moi-gioi/data`
11. `POST /api/admin/moi-gioi/search`
12. `POST /api/admin/moi-gioi/update`
13. `POST /api/admin/moi-gioi/delete`
14. `GET /api/admin/bds/data`
15. `POST /api/admin/bds/duyet`
16. `POST /api/admin/bds/change-status`
17. `POST /api/admin/bds/delete`
18. `POST /api/admin/bds/tim-kiem`
19. `GET /api/admin/goi-tin/data`
20. `POST /api/admin/goi-tin/create`
21. `POST /api/admin/goi-tin/update`
22. `POST /api/admin/goi-tin/delete`
23. `GET /api/admin/goi-tin/lich-su-mua`
24. `GET /api/admin/giao-dich/data`
25. `POST /api/admin/doanh-thu`
26. `POST /api/admin/user`
27. `GET /api/admin/chuc-vu/data`
28. `POST /api/admin/chuc-vu/create`
29. `POST /api/admin/chuc-vu/update`
30. `POST /api/admin/chuc-vu/delete`
31. `GET /api/admin/chuc-nang/data`
32. `GET /api/admin/phan-quyen/data/{id_chuc_vu}`
33. `POST /api/admin/phan-quyen/chuc-vu/create`
34. `POST /api/admin/phan-quyen/chuc-vu/delete`

### 9.3. Khách hàng

1. `POST /api/khach-hang/dang-nhap`
2. `POST /api/khach-hang/register`
3. `GET /api/khach-hang/profile`
4. `POST /api/khach-hang/update-profile`
5. `POST /api/khach-hang/update-password`
6. `POST /api/khach-hang/bds/yeu-thich`
7. `GET /api/khach-hang/bds/yeu-thich/data`
8. `POST /api/khach-hang/mua-goi`

### 9.4. Môi giới

1. `POST /api/moi-gioi/dang-nhap`
2. `POST /api/moi-gioi/dang-ky`
3. `GET /api/moi-gioi/profile`
4. `POST /api/moi-gioi/update-profile`
5. `POST /api/moi-gioi/update-password`
6. `POST /api/moi-gioi/logout`
7. `GET /api/moi-gioi/bds/data`
8. `POST /api/moi-gioi/bds/create`
9. `POST /api/moi-gioi/bds/update`
10. `POST /api/moi-gioi/bds/delete`
11. `GET /api/moi-gioi/goi-tin/data`
12. `POST /api/moi-gioi/goi-tin/mua`
13. `GET /api/moi-gioi/thong-bao`
14. `GET /api/moi-gioi/giao-dich/data`

## 10. Yêu cầu tích hợp frontend

1. Dùng token-based auth
2. Tách token admin và token client nếu cần
3. Tạo guard route frontend theo role
4. Hiển thị loading, empty state, error state cho mọi trang lấy API
5. Toast khi tạo, sửa, xóa, duyệt, mua gói thành công hoặc thất bại
6. Tất cả list API có pagination theo chuẩn Laravel
7. Các form cần validation UI đồng bộ với backend

## 11. Lưu ý quan trọng về backend hiện tại

Khi dựng frontend, hãy hiểu đây là hệ thống đang ở giai đoạn phát triển. Một số điểm cần chuẩn bị UI mềm dẻo:

1. API trả về `status` không đồng nhất, có nơi là `1/0`, có nơi là `success/error`
2. Module chatbot và AI định giá hiện mang tính placeholder, FE nên gắn nhãn `Beta`
3. Backend có quan hệ ảnh bất động sản nhưng chưa có API upload ảnh hoàn chỉnh, FE có thể dựng giao diện gallery và input URL ảnh hoặc uploader giả lập
4. Backend hiện có API đọc tỉnh thành, quận huyện và dữ liệu địa chỉ liên quan đến BĐS, nhưng chưa có flow CRUD địa chỉ đầy đủ cho môi giới. FE nên dựng form địa chỉ theo hướng linh hoạt: chọn tỉnh, chọn quận, nhập địa chỉ chi tiết và có thể chờ API hoàn thiện để lưu chuẩn
5. Route giao dịch của môi giới đã khai báo, FE nên thiết kế sẵn màn giao dịch cho môi giới
6. Module chức vụ và phân quyền đã có controller nhưng cần giữ UI linh hoạt vì backend phân quyền còn phụ thuộc dữ liệu admin thực tế

## 12. Kết quả mong muốn từ Stitch

Hãy tạo ra frontend hoàn chỉnh gồm:

1. Bộ màn hình Admin Portal đầy đủ theo mô tả ở trên
2. Bộ màn hình Client Portal đầy đủ cho guest, khách hàng và môi giới
3. Dùng Bootstrap 5 xuyên suốt
4. Có layout đẹp, rõ, hiện đại, đúng chất nền tảng bất động sản
5. Ưu tiên component tái sử dụng
6. Code dễ nối API Laravel ở bước sau

Nếu cần chọn mức ưu tiên, hãy ưu tiên build trước các màn:

1. Trang chủ public
2. Danh sách bất động sản
3. Chi tiết bất động sản
4. Đăng nhập và đăng ký client
5. Dashboard admin
6. Quản lý bất động sản admin
7. Tin của tôi cho môi giới
8. Gói tin và giao dịch
