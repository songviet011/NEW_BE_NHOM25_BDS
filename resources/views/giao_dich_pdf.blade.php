<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo giao dịch</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }
        h2 {
            text-align: center;
            color: #1B2559;
            margin-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            margin-bottom: 20px;
            color: #6c757d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h2>BÁO CÁO GIAO DỊCH</h2>
    <p class="subtitle">Ngày xuất: {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Mã GD</th>
                <th>Môi giới</th>
                <th>SĐT</th>
                <th>Gói tin</th>
                <th>Số tiền</th>
                <th>Phương thức</th> <!-- ✅ Đảm bảo có cột này -->
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item['ma_gd'] }}</td>
                <td>{{ $item['moi_gioi'] }}</td>
                <td>{{ $item['sdt'] }}</td>
                <td>{{ $item['goi_tin'] }}</td>
                <td>{{ $item['so_tien'] }}</td>
                <td>{{ $item['phuong_thuc'] }}</td> <!-- ✅ Hiển thị phương thức -->
                <td>{{ $item['trang_thai'] }}</td>
                <td>{{ $item['ngay_tao'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>