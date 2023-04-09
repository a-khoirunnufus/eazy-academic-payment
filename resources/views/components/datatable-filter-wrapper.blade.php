<div class="datatable-filter {{ $oneRow ? 'one-row' : 'multiple-row' }}">
    {{ $slot }}
    <div class="d-flex align-items-end">
        <button onclick="{{ $handler }}" class="btn btn-primary text-nowrap">
            <i data-feather="filter"></i>&nbsp;&nbsp;Filter
        </button>
    </div>
</div>