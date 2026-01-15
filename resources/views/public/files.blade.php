@extends('layouts.app')

@section('title', 'รายการไฟล์ - VoiceTrackBpl')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-file-audio text-primary"></i>
                        รายการไฟล์ที่พบ
                    </h5>
                    <small class="text-muted">รหัส: <strong class="text-uppercase">{{ $files->first()->code }}</strong></small>
                </div>
                <a href="{{ route('download.all', $files->first()->code) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> ดาวน์โหลดทั้งหมด ({{ $files->count() }} ไฟล์)
                </a>
            </div>
            <div class="card-body">
                <div class="mb-4 p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-md-4">
                            <strong><i class="fas fa-user"></i> ผู้ขอ:</strong>
                            <span class="ms-2">{{ $files->first()->requester }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-calendar-plus"></i> วันที่อัปโหลด:</strong>
                            <span class="ms-2">{{ $files->first()->uploaded_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong><i class="fas fa-calendar-times"></i> วันหมดอายุ:</strong>
                            <span class="ms-2 {{ $files->first()->isExpired() ? 'text-danger' : 'text-success' }}">
                                {{ $files->first()->expiry_date->format('d/m/Y') }}
                                @if(!$files->first()->isExpired())
                                    <span class="badge bg-success">ใช้งานได้</span>
                                @else
                                    <span class="badge bg-danger">หมดอายุ</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>ชื่อไฟล์</th>
                                <th width="150" class="text-center">ฟังตัวอย่าง</th>
                                <th width="150" class="text-center">ดาวน์โหลด</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $index => $file)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-file-audio text-primary me-2"></i>
                                    {{ $file->filename }}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" onclick="playAudio({{ $file->id }})">
                                        <i class="fas fa-play"></i> เล่น
                                    </button>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('download', $file->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> ดาวน์โหลด
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> ค้นหาไฟล์อื่น
            </a>
        </div>
    </div>
</div>

<div class="modal fade" id="audioModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-headphones text-primary"></i> เล่นไฟล์เสียง
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <audio id="audioPlayer" controls class="w-100">
                    <source src="" type="audio/wav">
                    เบราว์เซอร์ของคุณไม่รองรับการเล่นไฟล์เสียง
                </audio>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const audioModal = new bootstrap.Modal(document.getElementById('audioModal'));
    const audioPlayer = document.getElementById('audioPlayer');

    function playAudio(fileId) {
        const source = audioPlayer.querySelector('source');
        source.src = '{{ url('/') }}/stream/' + fileId;
        audioPlayer.load();
        audioModal.show();
        audioPlayer.play();
    }

    document.getElementById('audioModal').addEventListener('hidden.bs.modal', function () {
        audioPlayer.pause();
        audioPlayer.currentTime = 0;
    });
</script>
@endpush
@endsection
