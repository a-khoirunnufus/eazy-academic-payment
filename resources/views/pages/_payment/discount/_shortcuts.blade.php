<div class="eazy-shortcut mb-2">
    <a href="{{ route('payment.discount.index') }}" class="eazy-shortcut-item {{ $active == 'index' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="list"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Data Potongan</span>
        </div>
    </a>
    <a href="{{ route('payment.settings.credit-schema') }}" class="eazy-shortcut-item {{ $active == 'credit-schema' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="archive"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Penerima Potongan</span>
        </div>
    </a>
</div>
