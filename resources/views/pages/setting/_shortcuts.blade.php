<div class="eazy-shortcut mb-3">
    <a href="{{ url('setting/invoice-component') }}" class="eazy-shortcut-item {{ $active == 'invoice-component' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Setting Komponen<br>Tagihan</span>
        </div>
    </a>
    <a href="{{ url('setting/instalment-template') }}" class="eazy-shortcut-item {{ $active == 'instalment-template' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Setting Template<br>Cicilan</span>
        </div>
    </a>
    <a href="{{ url('setting/rates') }}" class="eazy-shortcut-item {{ $active == 'rates' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Setting Tarif</span>
        </div>
    </a>
    <a href="{{ url('setting/registration-form') }}" class="eazy-shortcut-item {{ $active == 'registration-form' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Setting Formulir<br>Pendaftaran(PMB)</span>
        </div>
    </a>
    <a href="{{ url('setting/academic-rules') }}" class="eazy-shortcut-item {{ $active == 'academic-rules' ? 'active' : '' }}">
        <div class="eazy-shortcut-icon">
            <i data-feather="settings"></i>
        </div>
        <div class="eazy-shortcut-label">
            <span>Setting Aturan<br>Akademik</span>
        </div>
    </a>
</div>