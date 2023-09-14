<div class="datatable-filter {{ $oneRow ? 'one-row' : 'multiple-row' }}">
    {{ $slot }}
    <div class="d-flex align-items-end">
        <button onclick="{{ $handler }}" class="btn btn-info text-nowrap">
            <i data-feather="filter"></i>&nbsp;&nbsp;Filter
        </button>
    </div>
</div>
