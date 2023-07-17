@extends('layouts.static_master')

@section('css_section')
<style>
    .space {
        margin-left: 10px;
    }
    .filter-container {
        min-width: 200px !important;
    }

    .target-print {
        display: none;
    }
</style>
@endsection

@section('page_title', 'Laporan Pembayaran Tagihan Mahasiswa Baru')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

@include('pages.report.new-student-invoice._shortcuts', ['active' => 'per-study-program'])

<div class="card">
    <div class="card-body">
        <div class="d-flex">
            <div class="filter-container">
                <label class="form-label">Tahun Akademik dan Semester</label>
                <select class="form-select select2" id="filterData">
                    <option value="#ALL">Semua Tahun Akademik dan Semester</option>
                    @foreach($year as $item)
                    <option value="{{ $item->msy_id }}">{{ $item->msy_year }} Semester {{$item->msy_semester}}</option>
                    @endforeach
                </select>
            </div>
            <div class="space filter-container">
                <label class="form-label">Fakultas</label>
                <select class="form-select select2" id="facultyFilter" onchange="getProdi()">
                    <option value="#ALL">Semua Fakultas</option>
                    @foreach($faculty as $item)
                    <option value="{{ $item->faculty_id }}">{{ $item->faculty_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space filter-container">
                <label class="form-label">Program Study</label>
                <select class="form-select select2" id="prodiFilter">
                    <option value="#ALL">Semua Program Study</option>
                </select>
            </div>
            <div class="align-self-end space">
                <button class="btn btn-primary" onclick="filter()">
                    <i data-feather="filter"></i>&nbsp;&nbsp;Filter
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <table id="new-student-invoice-table" class="table table-striped">
        <thead>
            <tr>
                <th rowspan="2">Tahun Akademik</th>
                <th rowspan="2">Program Studi / Fakultas</th>
                <th rowspan="2">Mahasiswa</th>
                <th colspan="4" class="text-center">Rincian</th>
                <th rowspan="2">
                    Total Harus Dibayar<br>
                    (A+B)-(C+D)
                </th>
                <th rowspan="2">Terbayar</th>
                <th rowspan="2">Piutang</th>
            </tr>
            <tr>
                <th>Tagihan(A)</th>
                <th>Denda(B)</th>
                <th>Beasiswa(C)</th>
                <th>Potongan(D)</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total Keseluruhan</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
                <th>Rp 100,000,000,00</th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="target-print">
    <table id="printTable" class="table table-bordered">
        <thead>
            <tr>
                <td>PROGRAM STUDI</td>
                <td>MAHASISWA</td>
                <td>RINCIAN</td>
                <td>PEMBAYARAN</td>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection

@section('js_section')
<script>
    var total_tagihan = 0;
    var total_denda = 0;
    var total_beasiswa = 0;
    var total_potongan = 0;
    var total_terbayar = 0;
    var total_harus_bayar = 0;
    var total_piutang = 0;
    var all_total_tagihan = 0;
    var all_total_denda = 0;
    var all_total_beasiswa = 0;
    var all_total_potongan = 0;
    var all_total_terbayar = 0;
    var all_total_harus_bayar = 0;
    var all_total_piutang = 0;
    var total_mahasiswa = 0;
    var total_mahasiswa_lunas = 0;
    var total_mahasiswa_belum_lunas = 0;

    var dt;
    var dataPrint = [];

    $(document).ready(function() {
        select2Replace();
    });

    $(function() {
        _oldStudentInvoiceTable.init()
    })

    const _oldStudentInvoiceTable = {
        ..._datatable,
        init: function(byFilter = '#ALL', faculty = '#ALL', prodi = '#ALL', searchData = '#ALL') {
            all_total_tagihan = 0;
            all_total_denda = 0;
            all_total_beasiswa = 0;
            all_total_potongan = 0;
            all_total_terbayar = 0;
            all_total_harus_bayar = 0;
            all_total_piutang = 0;
            var colsfoot = $($($('tfoot').children('tr')[0]).children('th'))

            dt = this.instance = $('#new-student-invoice-table').DataTable({
                serverSide: true,
                ajax: {
                    url: _baseURL + '/api/report/new-student-invoice',
                    data: {
                        data_filter: byFilter,
                        search_filter: searchData,
                        id_faculty: faculty,
                        id_prodi: prodi
                    },
                    dataSrc: function(json) {
                        setPrintTable(json.data)
                        dataPrint = json.data;
                        return json.data;
                    }
                },
                columns: [{
                        name: 'academic_year',
                        render: (data, _, row) => {
                            return this.template.titleWithSubtitleCell(row.year.msy_year, row.year.msy_semester);
                        }
                    },
                    {
                        name: 'study_program_name',
                        data: 'studyprogram_name',
                        render: (data, _, row) => {
                            return this.template.buttonLinkCell(data, {
                                link: _baseURL + '/report/new-student-invoice/program-study/' + row.studyprogram_id
                            });
                        }
                    },
                    {
                        name: 'student',
                        render: (data, _, row) => {
                            total_tagihan = 0;
                            total_denda = 0;
                            total_beasiswa = 0;
                            total_potongan = 0;
                            total_terbayar = 0;
                            total_harus_bayar = 0;
                            total_piutang = 0;
                            total_mahasiswa = 0;
                            total_mahasiswa_lunas = 0;
                            total_mahasiswa_belum_lunas = 0;

                            total_mahasiswa = row.student.length;
                            for (var i = 0; i < row.student.length; i++) {
                                total_tagihan += row.student[i].payment.prr_amount;
                                total_denda += row.student[i].payment.penalty;
                                total_beasiswa += row.student[i].payment.schoolarsip;
                                total_potongan += row.student[i].payment.discount;
                                total_harus_bayar += row.student[i].payment.prr_total;
                                total_terbayar += row.student[i].payment.prr_paid;
                                total_piutang = total_harus_bayar - total_terbayar;

                                if (row.student[i].payment.prr_total - row.student[i].payment.prr_paid > 0) {
                                    total_mahasiswa_belum_lunas++;
                                } else {
                                    total_mahasiswa_lunas++;
                                }
                            }

                            all_total_tagihan += total_tagihan;
                            colsfoot[1].innerHTML = this.template.currencyCell(all_total_tagihan);
                            all_total_denda += total_denda;
                            colsfoot[2].innerHTML = this.template.currencyCell(all_total_denda);
                            all_total_beasiswa += total_beasiswa;
                            colsfoot[3].innerHTML = this.template.currencyCell(all_total_beasiswa);
                            all_total_potongan += total_potongan;
                            colsfoot[4].innerHTML = this.template.currencyCell(all_total_potongan);
                            all_total_harus_bayar += total_harus_bayar;
                            colsfoot[5].innerHTML = this.template.currencyCell(all_total_harus_bayar);
                            all_total_terbayar += total_terbayar;
                            colsfoot[6].innerHTML = this.template.currencyCell(all_total_terbayar);
                            all_total_piutang += total_piutang;
                            colsfoot[7].innerHTML = this.template.currencyCell(all_total_piutang);

                            const listHeader = [{
                                    label: 'Lunas',
                                    value: total_mahasiswa_lunas
                                },
                                {
                                    label: 'Belum Lunas',
                                    value: total_mahasiswa_belum_lunas
                                }
                            ];
                            const listItem = [{
                                label: 'Jumlah Mahasiswa',
                                value: total_mahasiswa
                            }];
                            return this.template.listDetailCellV2(listItem, listHeader);
                        }
                    },
                    {
                        name: 'invoice_a',
                        render: (data, _, row) => {
                            return this.template.currencyCell(total_tagihan);
                        }
                    },
                    {
                        name: 'invoice_b',
                        render: (data, _, row) => {
                            return this.template.currencyCell(total_denda);
                        }
                    },
                    {
                        name: 'invoice_c',
                        render: (data, _, row) => {
                            return this.template.currencyCell(total_beasiswa);
                        }
                    },
                    {
                        name: 'invoice_d',
                        render: (data, _, row) => {
                            return this.template.currencyCell(total_potongan);
                        }
                    },
                    {
                        name: 'invoice_total',
                        render: (data, _, row) => {
                            return this.template.currencyCell(total_harus_bayar, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'paid_off_total',
                        render: (data, _, row) => {
                            return this.template.currencyCell(total_terbayar, {
                                bold: true
                            });
                        }
                    },
                    {
                        name: 'receivables_total',
                        render: (data, _, row) => {
                            return this.template.currencyCell(total_piutang, {
                                bold: true
                            });
                        }
                    }
                ],
                drawCallback: function(settings) {
                    feather.replace();
                },
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-0 row"' +
                    '<"col-sm-12 col-lg-auto d-flex justify-content-center justify-content-lg-start" <"old-student-invoice-actions">>' +
                    '<"col-sm-12 col-lg-auto row" <"col-md-auto d-flex justify-content-center justify-content-lg-end" <".search_filter">lB> >' +
                    '>' +
                    '<"eazy-table-wrapper" t>' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                buttons: [{
                    extend: 'collection',
                    text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-external-link font-small-4 me-50"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>Export</span>',
                    className: 'btn btn-outline-secondary dropdown-toggle',
                    buttons: [{
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard font-small-4 me-50"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>Pdf</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config) {
                                var element = document.getElementById('printTable');
                                console.log(element.innerHTML)
                                var printwin = window.open();
                                printwin.document.write(
                                    `<html>
                                    <head>
                                    <style>
                                    table, td, th {  
                                    border: 1px solid #ddd;
                                    text-align: left;
                                    }

                                    table {
                                    border-collapse: collapse;
                                    width: 100%;
                                    }

                                    th, td {
                                    padding: 5px;
                                    }
                                    </style>
                                    </head>
                                    <body>
                                    <table>
                                    ${element.innerHTML}
                                    </table>
                                    </body>
                                    </html>
                                    `
                                );
                                printwin.print();
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file font-small-4 me-50"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>Excel</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config) {
                                var formData = new FormData();
                                formData.append("data", JSON.stringify(dataPrint));
                                formData.append("_token", '{{csrf_token()}}');
                                var xhr = new XMLHttpRequest();
                                xhr.onload = function() {
                                    var downloadUrl = URL.createObjectURL(xhr.response);
                                    var a = document.createElement("a");
                                    document.body.appendChild(a);
                                    a.style = "display: none";
                                    a.href = downloadUrl;
                                    a.download = "Laporan Mahasiswa per Program Studi";
                                    a.click();
                                }
                                xhr.open("POST", _baseURL + "/api/report/old-student-invoice/export?type=new");
                                xhr.responseType = 'blob';
                                xhr.send(formData);
                            }
                        },
                        {
                            text: '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text font-small-4 me-50"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>Csv</span>',
                            className: 'dropdown-item',
                            action: function(e, dt, node, config) {
                                var csv = 'PROGRAM STUDI,LUNAS,BELUM LUNAS,JUMLAH MAHASISWA,TAGIHAN,DENDA,BEASISWA,POTONGAN,TOTAL HARUS BAYAR,TERBAYAR,PIUTANG\n';

                                var csvFileData = [];
                                for (var i = 0; i < dataPrint.length; i++) {
                                    var row = dataPrint[i];
                                    total_tagihan = 0;
                                    total_denda = 0;
                                    total_beasiswa = 0;
                                    total_potongan = 0;
                                    total_terbayar = 0;
                                    total_harus_bayar = 0;
                                    total_piutang = 0;
                                    total_mahasiswa = 0;
                                    total_mahasiswa_lunas = 0;
                                    total_mahasiswa_belum_lunas = 0;

                                    total_mahasiswa = row.student.length;
                                    for (var j = 0; j < row.student.length; j++) {
                                        total_tagihan += row.student[j].payment.prr_amount;
                                        total_denda += row.student[j].payment.penalty;
                                        total_beasiswa += row.student[j].payment.schoolarsip;
                                        total_potongan += row.student[j].payment.discount;
                                        total_harus_bayar += row.student[j].payment.prr_total;
                                        total_terbayar += row.student[j].payment.prr_paid;
                                        total_piutang = total_harus_bayar - total_terbayar;

                                        if (row.student[j].payment.prr_total - row.student[j].payment.prr_paid > 0) {
                                            total_mahasiswa_belum_lunas++;
                                        } else {
                                            total_mahasiswa_lunas++;
                                        }
                                    }

                                    csvFileData.push(
                                        [
                                            row.studyprogram_type + " " + row.studyprogram_name,
                                            total_mahasiswa_lunas,
                                            total_mahasiswa_belum_lunas,
                                            total_mahasiswa,
                                            total_tagihan,
                                            total_denda,
                                            total_beasiswa,
                                            total_potongan,
                                            total_harus_bayar,
                                            total_terbayar,
                                            total_piutang
                                        ]
                                    )
                                }

                                //merge the data with CSV  
                                csvFileData.forEach(function(row) {
                                    csv += row.join(',');
                                    csv += "\n";
                                });

                                var hiddenElement = document.createElement('a');
                                hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
                                hiddenElement.target = '_blank';

                                //provide the name for the CSV file to be downloaded  
                                hiddenElement.download = 'Laporan Mahasiswa per Program Studi.csv';
                                hiddenElement.click();
                            }
                        }
                    ]
                }],
                initComplete: function() {
                    $('.old-student-invoice-actions').html(`
                        <h5 class="mb-0">Daftar Tagihan</h5>
                    `)
                    $('.search_filter').html(`
                    <div class="dataTables_filter">
                        <label><input type="text" id="searchFilter" class="form-control" placeholder="Cari Data" onkeydown="searchData(event)"></label>
                    </div>
                    `)
                    feather.replace();
                }
            })
        },
        template: _datatableTemplates,
    }

    function filter() {
        var faculty = $('select[id="facultyFilter"]').val()
        var prodi = $('select[id="prodiFilter"]').val()
        dt.clear().destroy()
        _oldStudentInvoiceTable.init($('select[id="filterData"]').val(), faculty, prodi);
    }

    function getProdi() {
        $('#prodiFilter').html(`
                <option value="#ALL">Semua Program Study</option>
            `)

        var faculty = $('select[id="facultyFilter"]').val()
        if (faculty != '#ALL') {
            var xhr = new XMLHttpRequest()
            xhr.onload = function() {
                var data = JSON.parse(this.responseText);
                for (var i = 0; i < data.length; i++) {
                    $('#prodiFilter').append(`
                        <option value="${data[i].studyprogram_id}">${data[i].studyprogram_name}</option>
                    `)
                }
            }
            xhr.open('GET', _baseURL + '/api/report/getProdi/' + faculty);
            xhr.send()
        }
    }

    function searchData(event) {
        if (event.key == 'Enter') {
            var find = $('#searchFilter').val()
            $('#searchFilter').val('')

            find = find == '' ? '#ALL' : find;
            var faculty = $('select[id="facultyFilter"]').val()
            var prodi = $('select[id="prodiFilter"]').val()

            dt.clear().destroy()
            _oldStudentInvoiceTable.init($('select[id="filterData"]').val(), faculty, prodi, find);
        }
    }

    function setPrintTable(data, type = null) {
        var tbody = $('#printTable tbody');
        console.log(tbody);
        tbody.html('');

        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            total_tagihan = 0;
            total_denda = 0;
            total_beasiswa = 0;
            total_potongan = 0;
            total_terbayar = 0;
            total_harus_bayar = 0;
            total_piutang = 0;
            total_mahasiswa = 0;
            total_mahasiswa_lunas = 0;
            total_mahasiswa_belum_lunas = 0;

            total_mahasiswa = row.student.length;
            for (var j = 0; j < row.student.length; j++) {
                total_tagihan += row.student[j].payment.prr_amount;
                total_denda += row.student[j].payment.penalty;
                total_beasiswa += row.student[j].payment.schoolarsip;
                total_potongan += row.student[j].payment.discount;
                total_harus_bayar += row.student[j].payment.prr_total;
                total_terbayar += row.student[j].payment.prr_paid;
                total_piutang = total_harus_bayar - total_terbayar;

                if (row.student[j].payment.prr_total - row.student[j].payment.prr_paid > 0) {
                    total_mahasiswa_belum_lunas++;
                } else {
                    total_mahasiswa_lunas++;
                }
            }

            var overCols = type == null ? 5 : 4;
            var row = `<tr class="overcols">`
            row += `<td rowspan="${overCols}">${data[i].studyprogram_type} ${data[i].studyprogram_name}</td>`
            row += `<td>Lunas : ${total_mahasiswa_lunas}</td>`
            row += `<td>Tagihan : ${total_tagihan}</td>`
            row += `<td>Total Pembayaran : ${total_harus_bayar}<td>`
            row += `</tr>`

            row += `<tr>`
            row += `<td>Belum Lunas : ${total_mahasiswa_belum_lunas}</td>`
            row += `<td>Denda : ${total_denda}</td>`
            row += `<td>Terbayar : ${total_terbayar}</td>`
            row += `</tr>`

            row += `<tr>`
            row += `<td rowspan="${overCols-2}">Jumlah Mahasiswa : ${total_mahasiswa}</td>`
            row += `<td>Beasiswa : ${total_beasiswa}</td>`
            row += `<td rowspan="${overCols-2}">Piutang : ${total_piutang}</td>`
            row += `<tr>`

            row += `<tr>`
            row += `<td>Potongan : ${total_potongan}</td>`
            row += `</tr>`
            tbody.append(row);
        }

        var cols = document.querySelectorAll('.overcols');
        console.log(cols);
        for (var i = 0; i < cols.length; i++) {
            cols[i].querySelectorAll('td')[4].remove();
        }

    }
</script>
@endsection