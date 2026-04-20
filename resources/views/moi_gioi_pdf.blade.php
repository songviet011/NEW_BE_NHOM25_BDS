<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left
        }

        th {
            background: #f8f9fa;
            font-weight: bold
        }

        .header {
            text-align: center;
            margin-bottom: 15px
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>DANH SÁCH MÔI GIỚI</h2>
        <p>Ngày xuất: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>SĐT</th>
                <th>Tổng GD</th>
                <th>Doanh thu</th>
                <th>Ngày tham gia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $r)<tr>
                <td>{{ $r['id'] }}</td>
                <td>{{ $r['ten'] }}</td>
                <td>{{ $r['email'] }}</td>
                <td>{{ $r['sdt'] }}</td>
                <td>{{ $r['tong_gd'] }}</td>
                <td>{{ $r['doanh_thu'] }}</td>
                <td>{{ $r['ngay'] }}</td>
            </tr>@endforeach
        </tbody>
    </table>
</body>

</html>