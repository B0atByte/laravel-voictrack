@extends('layouts.app')

@section('title', 'ค้นหาไฟล์ - VoiceTrackBpl')

@section('content')
<div class="row justify-content-center" style="margin-top: 3rem;">
    <div class="col-md-6">
        <div class="card shadow-lg">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/BPL.png') }}" alt="BPL Logo" style="height: 100px; margin-bottom: 1.5rem;">
                    <h2 class="fw-bold">ค้นหาไฟล์เสียง</h2>
                    <p class="text-muted">กรอกรหัส 6 หลักเพื่อดาวน์โหลดไฟล์ของคุณ</p>
                </div>

                <form method="POST" action="{{ route('search') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="code" class="form-label fw-semibold">รหัสดาวน์โหลด</label>
                        <input type="text"
                               class="form-control form-control-lg text-center text-uppercase @error('code') is-invalid @enderror"
                               id="code"
                               name="code"
                               maxlength="6"
                               required
                               autofocus
                               placeholder="กรอกรหัส 6 หลัก"
                               style="letter-spacing: 0.3rem; font-size: 1.5rem; font-weight: 600;">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                        <i class="fas fa-search me-2"></i> ค้นหาไฟล์
                    </button>
                </form>

                <div class="mt-5 pt-4 border-top">
                    <h6 class="fw-bold mb-3">คำแนะนำการใช้งาน</h6>
                    <ul class="text-muted small">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>กรอกรหัส 6 หลักที่ได้รับ</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>ตรวจสอบวันหมดอายุของไฟล์</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>สามารถดาวน์โหลดไฟล์ทีละไฟล์ หรือทั้งหมดพร้อมกัน</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>ฟังตัวอย่างไฟล์ก่อนดาวน์โหลด</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted">
                <i class="fas fa-shield-alt"></i> ปลอดภัย |
                <i class="fas fa-clock"></i> รวดเร็ว |
                <i class="fas fa-lock"></i> เข้ารหัส
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('code').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    document.querySelector('form').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const code = document.getElementById('code').value;
            if (code.length === 6) {
                this.submit();
            }
        }
    });
</script>
@endpush
@endsection
