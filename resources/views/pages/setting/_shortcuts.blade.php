<div class="eazy-shortcut mb-2">
    <a href="{{ url('setting/invoice-component') }}" class="eazy-shortcut-item {{ $active == 'invoice-component' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="list"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Komponen Tagihan</span>
            <small class="text-secondary">Atur Komponen Tagihan</small>
        </div>
    </a>
    <a href="{{ url('setting/instalment-template') }}" class="eazy-shortcut-item {{ $active == 'instalment-template' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="archive"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Template Cicilan</span>
            <small class="text-secondary">Atur Skema Tagihan Cicilan</small>
        </div>
    </a>
    <a href="{{ url('setting/rates') }}" class="eazy-shortcut-item {{ $active == 'rates' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="activity"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tarif dan Pembayaran</span>
            <small class="text-secondary">Atur Tarif Tagihan Pembayaran</small>
        </div>
    </a>
    <a href="{{ url('setting/rates-per-course') }}" class="eazy-shortcut-item {{ $active == 'rates-per-course' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="book"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Tarif Per Mata Kuliah</span>
            <small class="text-secondary">Atur Permbayaran Per Mata Kuliah</small>
        </div>
    </a>
    <a href="{{ url('setting/registration-form') }}" class="eazy-shortcut-item {{ $active == 'registration-form' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="file-text"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Formulir Pendaftaran</span>
            <small class="text-secondary">Atur Pembayaran Formulir PMB</small>
        </div>
    </a>
    <a href="{{ url('setting/academic-rules') }}" class="eazy-shortcut-item {{ $active == 'academic-rules' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="pocket"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Aturan Akademik</span>
            <small class="text-secondary">Atur Pembayaran Akademik</small>
        </div>
    </a>
</div>