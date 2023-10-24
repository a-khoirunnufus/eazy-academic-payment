<div>
    <label class="form-label">{{ $title }}</label>
    <select id="{{ $elementId }}" class="form-select">
        <option value="#ALL" selected>Semua {{ $title }}</option>
    </select>
</div>

@push('elm_setup')

    caches.open('eazy-cache').then(cache => {
        cache.match(_baseURL+"/api/{{ $resourceName }}").then(async (response) => {

            let optionData = [];

            if(!response) {

                console.log("'{{ $title }}' cache not found, get data and store to cache.");
                optionData = await $.ajax({
                    async: true,
                    url: _baseURL+"/api/{{ $resourceName }}",
                    method: 'GET',
                    dataType: 'json',
                });
                cache.put(
                    _baseURL+"/api/{{ $resourceName }}",
                    new Response(JSON.stringify(optionData), {
                        headers: {
                            Date: new Date().toUTCString(),
                        },
                    })
                );

            } else {

                const date = new Date(response.headers.get('date'))
                // if cached file is older than 6 hours
                if ( Date.now() > date.getTime() + parseInt("{{ config('app.api_resource_cache_expiration') }}") ){

                    console.log("'{{ $title }}' cache expired, get data and update cache.");
                    optionData = await $.ajax({
                        async: true,
                        url: _baseURL+"/api/{{ $resourceName }}",
                        method: 'GET',
                        dataType: 'json',
                    });
                    cache.put(
                        _baseURL+"/api/{{ $resourceName }}",
                        new Response(JSON.stringify(optionData), {
                            headers: {
                                Date: new Date().toUTCString(),
                            },
                        })
                    );

                } else {

                    console.log("'{{ $title }}' cache found.");
                    optionData = await response.json();

                }

            }

            const valueFrom = "{{ $value }}";
            const labelTemplate = "{{ $labelTemplate }}";
            const labelTemplateItems = JSON.parse('{!! json_encode($labelTemplateItems) !!}');

            const formatted = await optionData.map(item => {
                let text = labelTemplate;
                for (const key of labelTemplateItems) {
                    text = text.replace(':'+key, item[key]);
                }
                return {
                    id: item[valueFrom],
                    text: text,
                };
            });

            $('#{{ $elementId }}').select2({
                data: [
                    {id: '_ALL', text: "Semua {{ $title }}"},
                    ...formatted,
                ]
            });
        })
    })

@endpush
