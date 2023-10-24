<div class="eazy-shortcut mb-3">
    <a
        href="{{ url('payment/report/old-student-invoice/studyprogram') }}"
        class="eazy-shortcut-item {{ $active == 'per-study-program' ? 'active' : '' }}"
    >
        <div class="eazy-shortcut-icon">
            <i data-feather="layers"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Detail Per Program Studi</span>
            <small class="text-secondary">Detail Tagihan Pembayaran Mahasiswa Lama Per Program Studi</small>
        </div>
    </a>
    <a
        href="{{ url('payment/report/old-student-invoice/student') }}"
        class="eazy-shortcut-item {{ $active == 'per-student' ? 'active' : '' }}"
    >
        <div class="eazy-shortcut-icon">
            <i data-feather="users"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Detail Per Mahasiswa</span>
            <small class="text-secondary">Detail Tagihan Pembayaran Per Mahasiswa Lama</small>
        </div>
    </a>
</div>
