// ==================== CONFIG SECTION ===============================

const _setDatatableConfig = () => {
    if(!$.fn.dataTable)
        return

    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        //dom: '<"datatable-header"f<"dt-buttons">l><"datatable-scroll"t><"datatable-footer"ip>',
        // dom: '<"datatable-header datatable-header-accent"lfB><"datatable-scroll-wrap"t><"datatable-footer"ip>',
        language: {
            search: '_INPUT_',
            searchPlaceholder: "Cari Data",
            lengthMenu: '_MENU_',
            paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' },
            "processing": "DataTables is currently busy",
            "sEmptyTable": "No data available in table", //Data tidak tersedia
            "sInfoEmpty":  "Showing 0", //Menampilkan
            "sLengthMenu": "Show _MENU_ entries", //Item per halaman
            "sInfoFiltered": " - from _MAX_ entries", //- dari total _MAX_ entri
            "sInfo": "Total: _TOTAL_ entries", //entri
            "sProcessing": "Processing...",
            "sZeroRecords": "No matching records found" //Tidak ditemukan data yang cocok,
        },
        "lengthMenu": [
            [10, 20, 30, 50, 100, 150, -1],
            [10, 20, 30, 50, 100, 150, "All"]
        ],
        "bStateSave": true,
        "pageLength": 30, // default records per page
        "autoWidth": false, // disable fixed width and enable fluid table
        "processing": true, // enable/disable display message box on record load
        "serverSide": true, // enable/disable server side ajax loading
        "order": [['1', 'asc']],
        dom:
            '<"d-flex justify-content-between align-items-center header-actions mx-0 row mt-75"' +
            '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" <"dtb">>' +
            '<"col-sm-12 col-lg-8 row" <"col-md-9 d-flex justify-content-center justify-content-md-end" f> <"col-md-3 d-flex justify-content-center justify-content-lg-end" lB> >' +
            '>t' +
            '<"d-flex justify-content-between mx-2 row mb-1"' +
            '<"col-sm-12 col-md-6"i>' +
            '<"col-sm-12 col-md-6"p>' +
            '>',
        lengthMenu: [
            [10, 20, 30, 50, 100, 150, -1],
            [10, 20, 30, 50, 100, 150, "All"]
        ],
        buttons: [{
            extend: 'collection',
            className: 'btn btn-outline-secondary dropdown-toggle',
            text: feather.icons['external-link'].toSvg({
                class: 'font-small-4 me-50'
            }) + 'Export',
            buttons: [{
                    extend: 'print',
                    text: feather.icons['printer'].toSvg({
                        class: 'font-small-4 me-50'
                    }) + 'Print',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'csv',
                    text: feather.icons['file-text'].toSvg({
                        class: 'font-small-4 me-50'
                    }) + 'Csv',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'excel',
                    text: feather.icons['file'].toSvg({
                        class: 'font-small-4 me-50'
                    }) + 'Excel',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'pdf',
                    text: feather.icons['clipboard'].toSvg({
                        class: 'font-small-4 me-50'
                    }) + 'Pdf',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'copy',
                    text: feather.icons['copy'].toSvg({
                        class: 'font-small-4 me-50'
                    }) + 'Copy',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5]
                    }
                }
            ],
            init: function(api, node, config) {
                /*$(node).removeClass('btn-secondary');
                $(node).parent().removeClass('btn-group');
                setTimeout(function() {
                    $(node).closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex');
                }, 50);*/
            }
        }],
        initComplete: function (settings, json) {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        }
    });
    $.fn.dataTableExt.oPagination.iFullNumbersShowPages = 10;
}
const _ajaxConfig = {
    showLoader: true,
    buttonLoader: null,

    set: function(){
        $.ajaxSetup({
            beforeSend : () => {
                if(this.showLoader)
                    document.getElementById("overlay").style.display = "block";
                else
                    this.buttonLoader.setLoadingIndicator()
            },
            complete : () => {
                if(this.showLoader){
                    document.getElementById("overlay").style.display = "none";
                } else {
                    this.buttonLoader.restore()
                    this.buttonLoader = null
                }
            },
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': _csrfToken
            }
        })
    },
    setButtonAsLoadingIndicator: function(elementSelector){
        this.showLoader = false
        this.buttonLoader = {
            element: $(elementSelector),
            prevHTML: null,
            setLoadingIndicator: function(){
                this.prevHTML = this.element.html()
                this.element.html(`
                    Loading ...
                `)
                this.element.attr({'disabled': 'disabled'})
            },
            restore: function(){
                this.element.removeAttr('disabled')
                this.element.html(this.prevHTML)
                this.prevHTML = null
            }
        }
    }
}
const _setFormDataJSONConfig = () => {
    if(!FormDataJson)
        return

    FormDataJson.defaultOptionsFromJson.triggerChangeEvent = true
}
const _setMomentConfig = () => {
    try {
        moment.locale('id')
        moment.updateLocale('id', {
            week: {
                dow : 1
            }
        });
    } catch(e) {}
}
const _setIconConfig = () => {
    try {
        feather.replace({
            width: 14,
            height: 14
        });
    } catch(e) {}
}


$(function(){
    _setDatatableConfig()
    _ajaxConfig.set()
    _setFormDataJSONConfig()
    _setMomentConfig()
    _setIconConfig()
})

// ==================== HELPER SECTION ===============================

/** 
 * DEBOUNCE HELPER
 */
const _debounce = (callback, time) => {
    if(window.timeOut)
        clearTimeout(window.timeOut)

    window.timeOut = setTimeout(function(){
        callback()
    }, time)
}
const _debounceSync = (time) => {
    return new Promise((resolve) => {
        _debounce(() => { resolve() }, time)
    })
}

/** 
 * DATATABLE HELPER
 */
const _datatable = {
    instance: null,
    reload: function(){
        this.instance.draw()
    },
    getRowData: function(e){
        return this.instance.row($(e).parents('tr')).data()
    },
    updateRowData: function(e, data){
        this.instance.row($(e).parents('tr')).data(data)
    },
    implementSearchDelay: function(_time = 500){
        let self = this
        let id = this.instance.table().node().id
        $(`#${id}_filter input`).unbind()
            .bind("input", async function(){
                await _debounceSync(_time)
                self.instance.search(this.value).draw()
            })
    }
}

/** 
 * SELECT2 HELPER
 */
const _select2AjaxUtil = {
    genereteQueryParams: (params, key) => {
        if(!params.searchColumns)
            return ''

        let query = {
            columns: [],
            search: {
                value: key
            },
            length: 10,
            start: 0
        }
        for(const item of params.searchColumns)
            query.columns.push({name: item, data: item})
        
        if(params.url.indexOf('?') !== -1)
            return `&${$.param(query)}`
        else
            return `?${$.param(query)}`
    },
    cache: {
        itemCount: 1,
        data: {},
        get: function(itemCount, key){
            if(!this.data[itemCount])
                return null
            if(!this.data[itemCount][key])
                return null
            return this.data[itemCount][key]
        },
        set: function(itemCount, key, data){
            if(!this.data[itemCount])
                this.data[itemCount] = {}

            this.data[itemCount][key] = data
        }
    }
}
const _select2AjaxWithDTOptions = (params) => {
    let itemCount = _select2AjaxUtil.cache.itemCount++
    return {
        ajax: {
            delay: 250,
            transport: async function(queryParams, successCallback) {
                let key = queryParams.data.term
                if(!key)
                    return successCallback([])

                let cachedData = _select2AjaxUtil.cache.get(itemCount, key)
                if(cachedData) {
                    successCallback(cachedData)
                } else {
                    $.get(params.url + _select2AjaxUtil.genereteQueryParams(params, key), (response) => {
                        let data = response.data.map((item) => {
                            return params.item(item)
                        })
                        _select2AjaxUtil.cache.set(itemCount, key, data)                        
                        successCallback(data)
                    })
                }
            },
            processResults: function(data) {
                return { results: data };
            }
        },
        minimumInputLength: 1,
        templateResult: function(e) {
            if (e.loading) return e.text;
            return $(`
                <div class='select2-result-repository clearfix'>
                    <span>${ e.text }</span>
                </div>`)
        },
        templateSelection: function(e) {
            return e.text
        },
        dropdownParent: params.dropdownParent ?? "body",
        allowClear: true,
        placeholder: params.placeholder
    }
}

/** 
 * TOASTR HELPER
 */
const _toastr = {
    options: {
        positionClass: 'toast-top-left',
        closeButton: true,
        tapToDismiss: false
    },
    success: function(message, title){
        toastr.success(message, title, this.options)
    },
    error: function(message, title){
        toastr.error(message, title, this.options)
    },
    info: function(message, title){
        toastr.info(message, title, this.options)
    },
    warning: function(message, title){
        toastr.warning(message, title, this.options)
    }
}

/** 
 * SWAL CONFIRMATION HELPER
 */
const _swalConfirm = (option) => {
    let defaultOptions = {
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: 'Tidak',
        confirmButtonText: 'Ya',
        customClass: {
            confirmButton: 'btn btn-blue',
            cancelButton: 'btn btn-outline-danger ms-1'
        },
        buttonsStyling: false
    }

    return Swal.fire({
        ...option,
        ...defaultOptions
    })
}
const _swalConfirmSync = (option) => {
    return new Promise((resolve) => {
        _swalConfirm(option).then((result) => {
            resolve(result.value)
        })  
    })
}

/** 
 * RESPONSE HANDLER RESPONSE
 */
const _responseHandler = {
    capitalizeFirstLetter: function(str){
        str = str.toLowerCase()
        return str.charAt(0).toUpperCase() + str.slice(1);
    },
    generalFailResponse: function(data){
        let response = data.responseJSON
                
        if(response.errors === undefined)
            return _toastr.error(response.message, 'Alert')

        if(typeof(response.errors) == 'object'){
            for(let i in response.errors){
                _toastr.error(
                    this.capitalizeFirstLetter(response.errors[i][0]), 
                    'Alert', 
                    toastrOptions
                )
            }
            return
        }

        _toastr.error('Terjadi kesalahan pada server', 'Alert')
    },
    formFailResponse: function(data, scopeElement = null){
        let response = data.responseJSON
                
        if(response.errors === undefined)
            return _toastr.error(response.message, 'Alert')

        if(typeof(response.errors) == 'object'){
            $(".form-alert").remove()
            for(let i in response.errors){
                let el = ""
                if(scopeElement == null){
                    el = $(`[name='${i}']`)
                } else {
                    el = $(`${scopeElement} [name='${i}']`)
                }
                // if element not found
                if(el.length == 0){
                    _toastr.error(
                        this.capitalizeFirstLetter(response.errors[i][0]), 
                        'Alert', 
                        toastrOptions
                    )
                } else {
                    el.parents('.form-group').append(`
                        <span class="form-alert text-danger">${this.capitalizeFirstLetter(response.errors[i][0])}</span>
                    `)
                }

            }
            return
        }

        _toastr.error('Terjadi kesalahan pada server', 'Alert')
    }
}