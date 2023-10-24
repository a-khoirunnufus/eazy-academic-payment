<div>
    <label class="form-label">{{ $title }}</label>
    <select id="{{ $selectId }}" class="form-select">
        @if($defaultValue)
            <option value="{{ $defaultValue }}" selected>{{ $defaultLabel }}</option>
        @endif
    </select>
</div>

@push('laravel-component-setup')
    <script>
        $(async function() {

            const data = await $.get({url: `${_baseURL}{{ $resourceUrl }}`, async: true});

            const valueFrom = "{{ $value }}";
            const labelTemplate = "{{ $labelTemplate }}";
            const labelTemplateItems = JSON.parse('{!! json_encode($labelTemplateItems) !!}');

            const formatted = data.map(item => {
                let text = labelTemplate;
                for (const labelItem of labelTemplateItems) {
                    if (typeof labelItem === 'object') {
                        text = text.replace(':'+labelItem.key, labelItem.mapping[item[labelItem.key]])
                    }
                    if (typeof labelItem === 'string') {
                        text = text.replace(':'+labelItem, item[labelItem]);
                    }
                }
                return {
                    id: item[valueFrom],
                    text: text,
                };
            });

            let select2Data = [];
            if ('{{ $withoutAllOption }}' == '0' || '{{ $withoutAllOption }}' == 'null' || '{{ $withoutAllOption }}' == '') {
                select2Data = [{id: '#ALL', text: "Semua {{ $title }}"}];
            }
            select2Data = [
                ...select2Data,
                ...formatted,
            ];

            $('#{{ $selectId }}').select2({
                data: select2Data,
                minimumResultsForSearch: 6,
            });

        });

    </script>
@endpush
