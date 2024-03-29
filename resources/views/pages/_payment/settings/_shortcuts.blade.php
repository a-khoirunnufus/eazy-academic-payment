<div class="eazy-shortcut mb-2">
    <a href="{{ route('payment.settings.component') }}" class="eazy-shortcut-item {{ $active == 'component' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="list"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Komponen<br>Tagihan</span>
        </div>
    </a>
    <a href="{{ route('payment.settings.credit-schema') }}" class="eazy-shortcut-item {{ $active == 'credit-schema' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="archive"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Template<br>Cicilan</span>
        </div>
    </a>
    <a href="{{ route('payment.settings.payment-rates') }}" class="eazy-shortcut-item {{ $active == 'payment-rates' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="activity"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tarif<br>dan Pembayaran</span>
        </div>
    </a>
    <a href="{{ route('payment.settings.subject-rates') }}" class="eazy-shortcut-item {{ $active == 'subject-rates' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="book"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tarif Per<br>Mata Kuliah</span>
        </div>
    </a>
    <a href="{{ url('setting/registration-form') }}" class="eazy-shortcut-item {{ $active == 'registration-form' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="file-text"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Formulir<br>Pendaftaran</span>
        </div>
    </a>
    <a href="{{ url('setting/academic-rules') }}" class="eazy-shortcut-item {{ $active == 'academic-rules' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="pocket"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Aturan<br>Akademik</span>
        </div>
    </a>
</div>
