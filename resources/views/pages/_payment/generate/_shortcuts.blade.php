<div class="eazy-shortcut mb-3">
    <a href="{{ route('payment.generate.student-invoice') }}" class="eazy-shortcut-item {{ $active == 'student-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="users"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan <br> Mahasiswa Lama</span>
        </div>
    </a>
    <a href="{{ route('payment.generate.new-student-invoice') }}" class="eazy-shortcut-item {{ $active == 'new-student-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="user"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan <br> Mahasiswa Baru</span>
        </div>
    </a>
    <a href="{{ route('payment.generate.discount') }}" class="eazy-shortcut-item {{ $active == 'discount' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="percent"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Potongan <br> Mahasiswa</span>
        </div>
    </a>
    <a href="{{ route('payment.generate.scholarship') }}" class="eazy-shortcut-item {{ $active == 'scholarship' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="layers"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Beasiswa <br> Mahasiswa</span>
        </div>
    </a>
    {{-- <a href="{{ url('generate/other-invoice') }}" class="eazy-shortcut-item {{ $active == 'other-invoice' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="clipboard"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tagihan <br> Lainnya</span>
        </div>
    </a> --}}
</div>
