<div class="eazy-shortcut mb-3">
    <a href="{{ url('generate/registrant-invoice') }}" class="eazy-shortcut-item {{ $active == 'registrant-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="mail"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Generate Tagihan<br>Pendaftar</span>
        </div>
    </a>
    <a href="{{ url('generate/old-student-invoice') }}" class="eazy-shortcut-item {{ $active == 'old-student-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="mail"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Generate Tagihan<br>Mahasiswa Lama</span>
        </div>
    </a>
    <a href="{{ url('generate/new-student-invoice') }}" class="eazy-shortcut-item {{ $active == 'new-student-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="mail"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Generate Tagihan<br>Mahasiswa Baru</span>
        </div>
    </a>
    <a href="{{ url('generate/other-invoice') }}" class="eazy-shortcut-item {{ $active == 'other-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="mail"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Generate Tagihan<br>Lainnya</span>
        </div>
    </a>
</div>