<div class="eazy-shortcut mb-2">
    <a href="{{ route('payment.approval.index') }}" class="eazy-shortcut-item {{ $active == 'manual' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="dollar-sign"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Pembayaran Manual</span>
        </div>
    </a>
    <a href="{{ route('payment.approval.dispensation.index') }}" class="eazy-shortcut-item {{ $active == 'dispensation' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="pause-circle"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Dispensasi Pembayaran</span>
        </div>
    </a>
    <a href="{{ route('payment.approval.credit.index') }}" class="eazy-shortcut-item {{ $active == 'credit' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="credit-card"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Pengajuan Kredit <br> Pembayaran</span>
        </div>
    </a>
</div>
