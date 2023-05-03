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
const _setDatepickerConfig = () => {
    $.fn.datepicker.dates['id'] = {
        days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"],
        daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
        daysMin: ["Mn", "Sn", "Sl", "Rb", "Km", "Jm", "Sb"],
        months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
        monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
        today: "Hari Ini",
        clear: "Clear",
        format: "dd-mm-yyyy",
        titleFormat: "MM yyyy",
        weekStart: 1
    };
}
const _setIconConfig = () => {
    try {
        feather.replace({
            width: 14,
            height: 14
        });
    } catch(e) {}
}

String.prototype.escape = function() {
    var tagsToReplace = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;'
    };
    return this.replace(/[&<>]/g, function(tag) {
        return tagsToReplace[tag] || tag;
    });
};

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
        console.log('callback called')
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
        // this.instance.ajax.reload(null, false)
    },
    getRowData: function(e){
        return this.instance.row($(e).parents('tr')).data()
    },
    updateRowData: function(e, data){
        this.instance.row($(e).parents('tr')).data(data)
    },
    /**
     * Customize datatable default search behaviour, wait for specific amount of time after latest
     * input action performed then perform search.
     */
    implementSearchDelay: function(_time = 1000){
        let self = this
        let id = this.instance.table().node().id
        $(`#${id}_filter input`).unbind()
            .bind("input", async function(){
                await _debounceSync(_time)
                self.instance.search(this.value).draw()
            })
    },
}

const _datatableTemplates = {
    defaultCell: function(data, {prefix = '', postfix = '', nowrap = true, bold = false, additionalClass = ''} = {}) {
        return `
            <span class="${nowrap ? 'text-nowrap' : ''} ${bold ? 'fw-bold' : ''} ${additionalClass}">
                ${prefix}${data}${postfix}
            </span>
        `;
    },
    currencyCell: function(data, {nowrap = true, bold = false, minus = false, additionalClass = ''} = {}) {
        return `
            <span class="${nowrap ? 'text-nowrap' : ''} ${bold ? 'fw-bold' : ''} ${additionalClass}">
                ${minus ? '- ' : ''}${Rupiah.format(data)}
            </span>
        `;
    },
    dateCell: function(data, {nowrap = true, bold = false, additionalClass = ''} = {}) {
        return `
            <span class="${nowrap ? 'text-nowrap' : ''} ${bold ? 'fw-bold' : ''} ${additionalClass}">
                ${moment(data).format('DD/MM/YYYY')}
            </span>
        `;
    },
    buttonLinkCell: function(label, {onclickFunc = null, link = null}, {nowrap = true, additionalClass = ''} = {}) {
        return `
            <div class="${additionalClass}">
                <a type="button" href="${link ? link : '#'}" ${onclickFunc ? 'onclick="'+onclickFunc+'"' : ''} class="btn btn-link px-0 ${nowrap ? 'text-nowrap' : ''}">
                    ${label}
                </a>
            </div>
        `;
    },
    badgeCell: function(label, bsColor, {centered = true, nowrap = true, additionalClass = ''} = {}) {
        return `
            <div class="d-flex ${centered ? 'justify-content-center' : ''}">
                <div class="badge bg-${bsColor} ${nowrap ? 'text-nowrap' : ''} ${additionalClass}" style="font-size: inherit">${label}</div>
            </div>
        `;
    },
    optionCheckCell: function(isChecked, {centered = true} = {}) {
        return `
            <div class="d-flex ${centered ? 'justify-content-center' : ''}">
                ${
                    isChecked ? `<div class="eazy-badge blue"><i data-feather="check"></i></div>`
                        : `<div class="eazy-badge red"><i data-feather="x"></i></div>`
                }
            </div>
        `;
    },
    titleWithSubtitleCell: function(title, subtitle, {nowrap = true} = {}) {
        return `
            <div>
                <span class="fw-bold ${nowrap ? 'text-nowrap' : ''}">${title}</span><br>
                <small class="text-secondary  ${nowrap ? 'text-nowrap' : ''}">${subtitle}</small>
            </div>
        `;
    },
    // TODO: migrate all implementation to listDetailCell()
    invoiceDetailCell: function(invoiceItems, invoiceTotal = null) {
        let html = '<div class="d-flex flex-column" style="gap: .5rem">'

        if(invoiceTotal) {
            html += `<div class="fw-bold text-nowrap">Total : ${Rupiah.format(invoiceTotal)}</div>`;
        }

        html += '<div class="d-flex flex-row" style="gap: 1rem">';

        const minItemPerColumn = 2;
        const half = invoiceItems.length > minItemPerColumn ? Math.ceil(invoiceItems.length/2) : invoiceItems.length;
        let firstCol = invoiceItems.slice(0, half);
        firstCol = firstCol.map(item => {
            return `
                <div class="text-secondary text-nowrap">${item.name} : ${Rupiah.format(item.nominal)}</div>
            `;
        }).join('');
        html += `<div class="d-flex flex-column" style="gap: .5rem">${firstCol}</div>`;

        if (half < invoiceItems.length) {
            let secondCol = invoiceItems.slice(half);
            secondCol = secondCol.map(item => {
                return `
                    <div class="text-secondary text-nowrap">${item.name} : ${Rupiah.format(item.nominal)}</div>
                `;
            }).join('');
            html += `<div class="d-flex flex-column" style="gap: .5rem">${secondCol}</div>`;
        }

        html += '</div></div>';
        return html;
    },
    listDetailCell: function(listItems, listHeader = null, itemValueFormatter = null) {
        let html = '<div class="d-flex flex-column" style="gap: .5rem">';
        if (listHeader) html += `<div class="fw-bold text-nowrap">${listHeader}</div>`;
        html += '<div class="d-flex flex-row" style="gap: 1rem">';

        const minItemPerColumn = 2;
        const half = listItems.length > minItemPerColumn ? Math.ceil(listItems.length/2) : listItems.length;
        let firstCol = listItems.slice(0, half);
        firstCol = firstCol.map(item => {
            return `
                <div class="text-secondary text-nowrap">${item.label} : ${itemValueFormatter ? itemValueFormatter(item.value) : item.value}</div>
            `;
        }).join('');
        html += `<div class="d-flex flex-column" style="gap: .5rem">${firstCol}</div>`;

        if (half < listItems.length) {
            let secondCol = listItems.slice(half);
            secondCol = secondCol.map(item => {
                return `
                    <div class="text-secondary text-nowrap">${item.label} : ${itemValueFormatter ? itemValueFormatter(item.value) : item.value}</div>
                `;
            }).join('');
            html += `<div class="d-flex flex-column" style="gap: .5rem">${secondCol}</div>`;
        }

        html += '</div></div>';
        return html;
    },
    listDetailCellV2: function(listItems, listHeader = null, itemValueFormatter = null) {
        let html = '<div class="d-flex flex-column" style="gap: .5rem">';

        if (listHeader) {
            html += `
                <div class="d-flex flex-column" style="gap: .5rem">
                    ${ listHeader.map(item => {
                            return `
                                <div class="fw-bold text-nowrap">${item.label} : ${itemValueFormatter ? itemValueFormatter(item.value) : item.value}</div>
                            `;
                        }).join('')
                    }
                </div>
            `;
        }

        html += '<div class="d-flex flex-row" style="gap: 1rem">';

        const minItemPerColumn = 2;
        const half = listItems.length > minItemPerColumn ? Math.ceil(listItems.length/2) : listItems.length;
        let firstCol = listItems.slice(0, half);
        firstCol = firstCol.map(item => {
            return `
                <div class="text-secondary text-nowrap">${item.label} : ${itemValueFormatter ? itemValueFormatter(item.value) : item.value}</div>
            `;
        }).join('');
        html += `<div class="d-flex flex-column" style="gap: .5rem">${firstCol}</div>`;

        if (half < listItems.length) {
            let secondCol = listItems.slice(half);
            secondCol = secondCol.map(item => {
                return `
                    <div class="text-secondary text-nowrap">${item.label} : ${itemValueFormatter ? itemValueFormatter(item.value) : item.value}</div>
                `;
            }).join('');
            html += `<div class="d-flex flex-column" style="gap: .5rem">${secondCol}</div>`;
        }

        html += '</div></div>';
        return html;
    },
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

const _options = {
    load: function({optionUrl, nameField, idData, nameData, val = null}){
        $.get(optionUrl, (data) => {
            JSON.parse(data).map(item => {
                $("[name="+nameField+"]").append(`
                    <option value="`+item[idData]+`">`+item[nameData]+`</option>
                `)
            })
            val ? $("[name="+nameField+"]").val(val) : ""
            $("[name="+nameField+"]").trigger('change')
            selectRefresh()
        })
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
    /**
     * Capitalize first letter for given string
     *
     * @param {string} str
     *
     * @return {string}
     */
    capitalizeFirstLetter: function(str){
        str = str.toLowerCase()
        return str.charAt(0).toUpperCase() + str.slice(1);
    },
    /**
     *
     * @param {jqXHR} data
     * @returns
     */
    generalFailResponse: function(data){
        console.log(data);
        let response = data.responseJSON

        if(response.errors === undefined)
            return _toastr.error(response.message, 'Alert')

        if(typeof(response.errors) == 'object'){
            for(let i in response.errors){
                _toastr.error(
                    this.capitalizeFirstLetter(response.errors[i][0]),
                    'Alert',
                    _toastr.options
                )
            }
            return
        }

        _toastr.error('Terjadi kesalahan pada server', 'Alert')
    },
    formFailResponse: function(data, scopeElement = null){
        let response = data.responseJSON

        if(response.errors === undefined)
            // input validation errors not exist
            return _toastr.error(response.message, 'Alert')

        /**
         * Display error message at bottom of input element
         */
        if(typeof(response.errors) == 'object'){
            $(".form-alert").remove()
            for(let i in response.errors){
                let el = undefined;
                if(scopeElement == null){
                    if (i.split('.')[1]) {
                        // field is array
                        const [fieldKey, arrIdx] = i.split('.');
                        el = $(`[name='${fieldKey}[]']`).eq(arrIdx);
                    } else {
                        // field is not array
                        el = $(`[name='${i}']`);
                    }
                } else {
                    el = $(`${scopeElement} [name='${i}']`)
                }
                // if element not found
                if( el.length == 0 ){
                    _toastr.error(
                        this.capitalizeFirstLetter(response.errors[i][0]),
                        'Alert',
                        _toastr.options
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

/**
 * MODAL HELPER
 */
const mainModalElm = new bootstrap.Modal(document.getElementById('mainModal'));

$('#mainModal').on('hidden.bs.modal', function (event) {
    Modal.close();
});

class Modal {
    /**
     * Displaying modal to user
     *
     * @params {string} type - type of modal content, 'detail' modal or 'form' modal.
     * @params {string} modalTitle - label for modal title.
     * @params {object} config - modal content configurations.
     * @params {string} config.formId - id attribute of created form.
     * @params {string} config.formActionUrl - url when submitting form.
     * @params {object} config.fields - form fields configurations.
     * @params {boolean} config.fields.[db_field_name].isHidden - set visibility of fields.
     * @params {string} config.fields.[db_field_name].content.template - string of prepared html element.
     * @params {string} config.fields.[db_field_name].content.[parameter] - parameter for html element.
     * @params {string} config.formSubmitLabel - form submit button label.
     * @params {function} config.callback - action after form submit success.
     */
    static show({type, modalTitle, modalSize = null, config}) {
        $(`#mainModal #mainModalLabel`).text(modalTitle);

        if(modalSize) {
            $('#mainModal .modal-dialog').addClass('modal-'+modalSize);
        } else {
            $('#mainModal .modal-dialog').addClass('modal-md');
        }

        var html = undefined;
        if(type === 'detail') {
            html = this.generateModalDetailBody(config);
            $(`#mainModal .modal-body`).html(html);
            config.callback();

        } else if(type === 'form') {
            html = this.generateModalFormBody(config);
            $(`#mainModal .modal-body`).html(html);
            config.modalShow && config.modalShow();
            $('#'+config.formId).on(
                'submit',
                this.makeFormSubmitHandler(config.formId, config.formActionUrl, config.beforeSubmit, config.callback, config.rowId, config.data),
            );
        } else if(type == 'confirmation') {
            html = this.generateModalConfirmationBody(config);
            $(`#mainModal .modal-body`).html(html);
            config.callback();
        }

        feather.replace();
        $('.flatpickr-basic').flatpickr();
        select2Replace();
        mainModalElm.show();
    }

    static generateModalDetailBody(config) {
        var fieldConfig = config.fields;
        var html = '<div class="d-flex flex-column" style="gap: 1.5rem">';

        for (var key in fieldConfig) {
            var title = fieldConfig[key].title;
            var contentConfig = fieldConfig[key].content;
            var contentHtml = contentConfig.template;
            delete contentConfig.template

            for(var x in contentConfig) {
                contentHtml = contentHtml.replace(':'+x, contentConfig[x].escape());
            }

            if(fieldConfig[key].isHidden) {
                html += '';
            } else {
                html += `
                    <div>
                        <div class="fw-bold" style="margin-bottom: .5rem">${title}</div>
                        <div>${contentHtml}</div>
                    </div>
                `;
            }
        }

        html += '</div>';
        return html;
    }

    static generateModalFormBody(config) {
        var fieldConfig = config.fields;
        var formType = config.formType ?? 'add';
        var isTwoColumn = config.isTwoColumn ?? false;
        var formTemplate = (formContent) => {
            return `<form id="${config.formId}">
                ${formContent}
            </form>`;
        }
        var formContentHtml = '';

        for (var key in fieldConfig) {
            var title = fieldConfig[key].title ?? '';
            var fieldType = fieldConfig[key].type ?? 'default';
            var contentConfig = fieldConfig[key].content;
            var contentHtml = contentConfig.template;
            delete contentConfig.template

            for(var x in contentConfig) {
                contentHtml = contentHtml.replace(':'+x, contentConfig[x].escape());
            }

            if(fieldConfig[key].isHidden) {
                formContentHtml += contentHtml;
            } else {
                if(fieldType == 'default') {
                    formContentHtml += `
                        <div class="form-group">
                            <label class="form-label-md">${title}</label>
                            ${contentHtml}
                        </div>
                    `;
                } else if (fieldType == 'checkbox') {
                    formContentHtml += `
                        <div class="form-group">
                            ${contentHtml}
                        </div>
                    `;
                } else if (fieldType == 'custom-field'){
                    formContentHtml += `
                        ${contentHtml}
                    `;
                }
            }
        }

        if (isTwoColumn) {
            formContentHtml = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem">
                    ${formContentHtml}
                </div>
            `;
        } else {
            formContentHtml = `
                <div style="display: flex; flex-direction: column; gap: 1.5rem">
                    ${formContentHtml}
                </div>
            `;
        }

        var formSubmitNote = config.formSubmitNote ?? false;

        if(formSubmitNote){
            var formActionHtml = `
                <div class="d-flex align-items-center flex-wrap justify-content-between mt-4" style="gap:10px">
                    ${formSubmitNote}
                    <span>
                    <button type="submit" class="btn ${formType == 'add' ? 'btn-success' : 'btn-warning'} me-1">${config.formSubmitLabel}</button>
                    <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-outline-secondary">Batal</a>
                    </span>
                </div>
            `;
        }else{
            var formActionHtml = `
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn ${formType == 'add' ? 'btn-success' : 'btn-warning'} me-1">${config.formSubmitLabel}</button>
                    <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-outline-secondary">Batal</a>
                </div>
            `;
        }

        return formTemplate(formContentHtml + formActionHtml);
    }

    static generateModalConfirmationBody(config) {
        var html = '<div>';
        html += config.modalBody;
        html += `
            <div class="d-flex justify-content-end mt-2">
                <button class="btn btn-danger me-1">Hapus</button>
                <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-outline-secondary">Batal</a>
            </div>
        `;
        html += '</div>';

        return html;
    }

    static makeFormSubmitHandler(formId, formActionUrl, beforeSubmit, callback, rowId = null, data = null) {
        return async (e) => {
            e.preventDefault();

            try {
                if(beforeSubmit) await beforeSubmit();
                var formData = null;
                data ? formData = data : formData = new FormData($('#'+formId)[0]);
                rowId ? formData.append('msc_id', rowId) : "";

                $.ajax({
                    url: formActionUrl,
                    type:'post',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType:'json',
                    success : (data, textStatus, jqXHR) => {
                        if (data.success == true) {
                            Swal.fire({
                                icon: 'success',
                                text: data.message,
                            }).then(() => {
                                this.close();
                                callback();
                            });
                        } else if (data.success == false) {
                            Swal.fire({
                                icon: 'error',
                                text: data.message,
                            });
                        } else {
                            _responseHandler.generalFailResponse(jqXHR);
                        }
                    },
                    error: function(data){
                        _responseHandler.formFailResponse(data);
                    },
                });

            } catch (error) {
                _responseHandler.generalFailResponse(jqXHR);
            }

        }
    }

    static close() {
        var formId = $('#mainModal').find('form').attr('id');
        formId && $('#'+formId).unbind('submit');

        $('#mainModal .modal-dialog').attr('class', 'modal-dialog modal-dialog-centered');
        $(`#mainModal #mainModalLabel`).text('...');
        $(`#mainModal .modal-body`).html('...');
        mainModalElm.hide();
    }
}

/**
 * SELECT2
 */
function select2Replace() {
    $('select[eazy-select2-active]').each(function() {
        $(this).select2({
            minimumResultsForSearch: -1,
        });
    });
}

function selectRefresh() {
    $('.select2').select2({
        placeholder: "Pilih Opsi yang Tersedia",
        dropdownParent: $("#mainModal"),
    });
};

/**
 * Localization
 */
let Rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
});
