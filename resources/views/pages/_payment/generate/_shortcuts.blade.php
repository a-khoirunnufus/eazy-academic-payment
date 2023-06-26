<div class="eazy-shortcut mb-3">
    <a href="{{ url('generate/registrant-invoice') }}" class="eazy-shortcut-item {{ $active == 'registrant-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="list"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan Pendaftar</span>
        </div>
    </a>
    <a href="{{ route('payment.generate.student-invoice') }}" class="eazy-shortcut-item {{ $active == 'student-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="users"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan Mahasiswa Lama</span>
        </div>
    </a>
    <a href="{{ route('payment.generate.new-student-invoice') }}" class="eazy-shortcut-item {{ $active == 'new-student-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="user"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan Mahasiswa Baru</span>
        </div>
    </a>
    <a href="{{ route('payment.generate.discount') }}" class="eazy-shortcut-item {{ $active == 'discount' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="percent"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Potongan Mahasiswa</span>
        </div>
    </a>
    <a href="{{ url('generate/other-invoice') }}" class="eazy-shortcut-item {{ $active == 'other-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="clipboard"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan Lainnya</span>
        </div>
    </a>
</div>
