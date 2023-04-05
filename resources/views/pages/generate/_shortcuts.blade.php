<div class="eazy-shortcut mb-3">
    <a href="{{ url('generate/registrant-invoice') }}" class="eazy-shortcut-item {{ $active == 'registrant-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="list"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan Pendaftar</span>
            <small class="text-secondary">Generate Tagihan Pendaftar</small>
        </div>
    </a>
    <a href="{{ url('generate/old-student-invoice') }}" class="eazy-shortcut-item {{ $active == 'old-student-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="users"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan Mahasiswa Lama</span>
            <small class="text-secondary">Generate Tagihan Mahasiswa Lama</small>
        </div>
    </a>
    <a href="{{ url('generate/new-student-invoice') }}" class="eazy-shortcut-item {{ $active == 'new-student-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="user"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan Mahasiswa Baru</span>
            <small class="text-secondary">Generate Tagihan Mahasiswa Baru</small>
        </div>
    </a>
    <a href="{{ url('generate/other-invoice') }}" class="eazy-shortcut-item {{ $active == 'other-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="clipboard"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan Lainnya</span>
            <small class="text-secondary">Generate Tagihan Lainnya</small>
        </div>
    </a>
</div>