<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            color: #1B2559;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-item {
            flex: 1;
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
        }

        .stat-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .success {
            color: #198754;
        }

        .pending {
            color: #ffc107;
        }

        .failed {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <h2>CHI TIẾT MÔI GIỚI</h2>
    <p class="subtitle">Ngày xuất: {{ now()->format('d/m/Y H:i') }}</p>

    <div class="info-box">
        <strong>Thông tin cá nhân:</strong><br>
        Họ tên: {{ $data['moi_gioi']->ten }}<br>
        Email: {{ $data['moi_gioi']->email }}<br>
        SĐT: {{ $data['moi_gioi']->so_dien_thoai }}
    </div>

    <div class="stats">
        <div class="stat-item">
            <div class="stat-value">{{ $data['tong_don'] }}</div>
            <div class="stat-label">Tổng đơn</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $data['don_active'] }}</div>
            <div class="stat-label">Đang active</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($data['tong_tien'], 0, ',', '.') }} đ</div>
            <div class="stat-label">Tổng tiền</div>
        </div>
    </div>

    <h3 style="margin-top: 30px; border-bottom: 2px solid #0d6efd; padding-bottom: 5px;">Lịch sử giao dịch</h3>

    <table>
        <thead>
            <tr>
                <th>Mã GD</th>
                <th>Gói tin</th>
                <th>Số tiền</th>
                <th>Phương thức</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['giao_dichs'] as $gd)
            <tr>
                <td>{{ $gd->ma_giao_dich }}</td>
                <td>{{ $gd->goiTin->ten_goi ?? '' }}</td>
                <td>{{ number_format($gd->so_tien, 0, ',', '.') }} đ</td>
                <td>{{ $gd->phuong_thuc ?? 'N/A' }}</td>
                <td class="{{ 
                    $gd->trang_thai === 'success' ? 'success' : 
                    ($gd->trang_thai === 'pending' ? 'pending' : 'failed') 
                }}">
                    {{
                        $gd->trang_thai === 'success' ? 'Thành công' : 
                        ($gd->trang_thai === 'pending' ? 'Chờ xử lý' : 'Thất bại') 
                    }}
                </td>
                <td>{{ $gd->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>