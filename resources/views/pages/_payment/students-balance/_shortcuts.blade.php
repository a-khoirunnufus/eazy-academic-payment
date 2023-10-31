<div class="eazy-shortcut mb-2">
    <a href="{{ route('payment.students-balance.index') }}" class="eazy-shortcut-item {{ $active == 'index' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="list"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>List Saldo Mahasiswa</span>
        </div>
    </a>
    <a href="{{ route('payment.students-balance.withdraw') }}" class="eazy-shortcut-item {{ $active == 'withdraw' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="list"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Penarikan Saldo Mahasiswa</span>
        </div>
    </a>
</div>
