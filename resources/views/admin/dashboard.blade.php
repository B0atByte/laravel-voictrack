@extends('layouts.app')

@section('title', 'แดชบอร์ดผู้ดูแล - VoiceTrackBpl')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center mb-2">
            <img src="{{ asset('images/BPL.png') }}" alt="BPL Logo" style="height: 50px; margin-right: 15px;">
            <div>
                <h2 class="fw-bold mb-0">
                    <i class="fas fa-tachometer-alt text-primary"></i> แดชบอร์ดผู้ดูแลระบบ
                </h2>
                <p class="text-muted mb-0">จัดการไฟล์เสียงและตรวจสอบสถิติการใช้งาน</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-file-audio fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold">{{ $stats['total'] }}</h3>
                <p class="text-muted mb-0">ไฟล์ทั้งหมด</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h3 class="fw-bold text-success">{{ $stats['active'] }}</h3>
                <p class="text-muted mb-0">ไฟล์ใช้งานได้</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                <h3 class="fw-bold text-danger">{{ $stats['expired'] }}</h3>
                <p class="text-muted mb-0">ไฟล์หมดอายุ</p>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-cloud-upload-alt"></i> อัปโหลดไฟล์ใหม่</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="files" class="form-label">เลือกไฟล์เสียง <span class="text-danger">*</span></label>
                    <input type="file"
                           class="form-control @error('files.*') is-invalid @enderror"
                           id="files"
                           name="files[]"
                           multiple
                           accept=".wav,.mp3,.ogg,.m4a"
                           required>
                    <small class="text-muted">รองรับ: WAV, MP3, OGG, M4A (สูงสุด 50MB ต่อไฟล์)</small>
                    @error('files.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="requester" class="form-label">ชื่อผู้ขอ <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('requester') is-invalid @enderror"
                           id="requester"
                           name="requester"
                           placeholder="กรอกชื่อผู้ขอไฟล์"
                           required>
                    @error('requester')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2 mb-3">
                    <label for="expiry_days" class="form-label">จำนวนวัน <span class="text-danger">*</span></label>
                    <select class="form-select @error('expiry_days') is-invalid @enderror"
                            id="expiry_days"
                            name="expiry_days"
                            required>
                        <option value="1">1 วัน</option>
                        <option value="2">2 วัน</option>
                        <option value="3" selected>3 วัน</option>
                        <option value="4">4 วัน</option>
                        <option value="5">5 วัน</option>
                        <option value="6">6 วัน</option>
                        <option value="7">7 วัน</option>
                    </select>
                    @error('expiry_days')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-upload"></i> อัปโหลด
                    </button>
                </div>
            </div>
            <div id="fileList" class="mt-2"></div>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list"></i> รายการไฟล์ทั้งหมด</h5>
        <div class="btn-group">
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteExpiredModal">
                <i class="fas fa-trash"></i> ลบไฟล์หมดอายุ
            </button>
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
                <i class="fas fa-trash-alt"></i> ลบทั้งหมด
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 mb-3">
            <div class="col-md-4">
                <input type="text"
                       class="form-control"
                       name="search"
                       placeholder="ค้นหา (รหัส, ผู้ขอ, ชื่อไฟล์)"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">ทุกสถานะ</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งานได้</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>หมดอายุ</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary w-100">
                    <i class="fas fa-redo"></i> รีเซ็ต
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th width="100">รหัส</th>
                        <th>ชื่อไฟล์</th>
                        <th width="150">ผู้ขอ</th>
                        <th width="130">วันที่อัปโหลด</th>
                        <th width="120">วันหมดอายุ</th>
                        <th width="100" class="text-center">สถานะ</th>
                        <th width="100" class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($files as $index => $file)
                    <tr>
                        <td>{{ $files->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-dark">{{ $file->code }}</span>
                        </td>
                        <td>
                            <i class="fas fa-file-audio text-primary me-1"></i>
                            <small>{{ Str::limit($file->filename, 40) }}</small>
                        </td>
                        <td>{{ $file->requester }}</td>
                        <td>
                            <small>{{ $file->uploaded_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <small>{{ $file->expiry_date->format('d/m/Y') }}</small>
                        </td>
                        <td class="text-center">
                            @if($file->isExpired())
                                <span class="badge bg-danger">หมดอายุ</span>
                            @else
                                <span class="badge bg-success">ใช้งานได้</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('admin.delete', $file->id) }}" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์นี้?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>ไม่พบข้อมูลไฟล์</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($files->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $files->links() }}
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="deleteExpiredModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-warning"></i> ยืนยันการลบไฟล์หมดอายุ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณแน่ใจหรือไม่ว่าต้องการลบไฟล์ที่หมดอายุทั้งหมด?</p>
                <p class="text-danger"><strong>การกระทำนี้ไม่สามารถย้อนกลับได้!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form method="POST" action="{{ route('admin.deleteAll') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="type" value="expired">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> ลบไฟล์หมดอายุ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> คำเตือน: ลบทั้งหมด</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger"><strong>คุณกำลังจะลบไฟล์ทั้งหมดในระบบ!</strong></p>
                <p>การกระทำนี้จะลบไฟล์ทั้งหมด ทั้งที่ใช้งานได้และหมดอายุ</p>
                <p class="text-danger"><strong>การกระทำนี้ไม่สามารถย้อนกลับได้!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form method="POST" action="{{ route('admin.deleteAll') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="type" value="all">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> ยืนยันลบทั้งหมด
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('files').addEventListener('change', function(e) {
        const fileList = document.getElementById('fileList');
        const files = e.target.files;

        if (files.length > 0) {
            let html = '<div class="alert alert-info"><strong>ไฟล์ที่เลือก:</strong><ul class="mb-0 mt-2">';
            for (let i = 0; i < files.length; i++) {
                const sizeMB = (files[i].size / (1024 * 1024)).toFixed(2);
                html += `<li>${files[i].name} (${sizeMB} MB)</li>`;
            }
            html += '</ul></div>';
            fileList.innerHTML = html;
        } else {
            fileList.innerHTML = '';
        }
    });

    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            setTimeout(() => bsAlert.close(), 5000);
        });
    }, 100);
</script>
@endpush
@endsection
