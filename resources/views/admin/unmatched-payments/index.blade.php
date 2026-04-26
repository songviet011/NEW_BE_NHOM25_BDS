@extends('admin.layout') {{-- Thay bằng layout admin của bạn --}}

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">⚠️ Giao Dịch Chưa Khớp</h2>
        <span class="badge bg-warning text-dark fs-6">{{ $unmatched->total() }} chưa xử lý</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Thời gian</th>
                            <th>Số tiền</th>
                            <th>Mã từ Sepay</th>
                            <th>Sepay Reference</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unmatched as $item)
                        <tr>
                            <td class="ps-4">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td><strong class="text-success">{{ number_format($item->so_tien, 0, ',', '.') }}đ</strong></td>
                            <td><code>{{ $item->order_code_from_sepay }}</code></td>
                            <td><code>{{ $item->sepayer_reference }}</code></td>
                            <td><span class="badge bg-warning text-dark">{{ $item->status }}</span></td>
                            <td class="text-end pe-4">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Khớp thủ công
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;">
                                        <li class="px-2 pb-2 border-bottom mb-1">
                                            <small class="text-muted">Chọn giao dịch pending:</small>
                                        </li>
                                        @forelse($pendingTransactions as $tx)
                                        <li>
                                            <form method="POST" action="{{ route('admin.unmatched.match', $item->id) }}" class="d-flex gap-2 align-items-center py-1">
                                                @csrf
                                                <input type="hidden" name="giao_dich_id" value="{{ $tx->id }}">
                                                <button type="submit" class="btn btn-sm btn-success flex-grow-1 text-start">
                                                    <strong>{{ $tx->ma_giao_dich }}</strong> - {{ number_format($tx->so_tien) }}đ
                                                </button>
                                            </form>
                                        </li>
                                        @empty
                                        <li class="text-center text-muted py-2">Không có giao dịch pending</li>
                                        @endforelse
                                    </ul>
                                </div>
                                <form method="POST" action="{{ route('admin.unmatched.ignore', $item->id) }}" class="d-inline ms-1">
                                    @csrf
                                    <input type="hidden" name="reason" value="Admin bỏ qua">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Bỏ qua</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                Không có giao dịch nào cần xử lý!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $unmatched->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection