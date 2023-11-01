<div class="eazy-shortcut mb-2">
    <a href="{{ route('payment.student-balance.index') }}" class="eazy-shortcut-item {{ $active == 'index' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="list"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Saldo Saya</span>
        </div>
    </a>
    <a href="{{ route('payment.student-balance.withdraw') }}" class="eazy-shortcut-item {{ $active == 'withdraw' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="list"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Riwayat Penarikan Saldo</span>
        </div>
    </a>
</div>
