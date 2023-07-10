@extends('layouts.static_master')

@section('page_title', 'Laporan Piutang Mahasiswa Baru')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('css_section')
<style>
    .eazy-summary {
        display: flex;
        flex-direction: row;
        gap: 2rem;
        justify-content: space-between;
    }

    .eazy-summary__item {
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    .eazy-summary__item .item__icon {
        color: blue;
        background-color: lightblue;
        border-radius: 50%;
        height: 56px;
        width: 56px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 1rem;
    }

    .eazy-summary__item .item__icon.item__icon--blue {
        color: #356CFF;
        background-color: #F0F4FF;
    }

    .eazy-summary__item .item__icon.item__icon--green {
        color: #0BA44C;
        background-color: #E1FFE0;
    }

    .eazy-summary__item .item__icon.item__icon--red {
        color: #FF4949;
        background-color: #FFF5F5;
    }

    .eazy-summary__item .item__icon svg {
        height: 30px;
        width: 30px;
    }

    .eazy-summary__item .item__text span:first-child {
        display: block;
        font-size: 1rem;
    }

    .eazy-summary__item .item__text span:last-child {
        display: block;
        font-size: 18px;
        font-weight: 700;
    }

    .space {
        margin-left: 10px;
    }

    .filter-container {
        min-width: 200px !important;
    }
</style>
@endsection

@section('content')

@include('pages.report.new-student-receivables._shortcuts', ['active' => 'per-study-program'])

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
    <div class="card-body">
        <div id="receivables-summary" class="eazy-summary">
            <div class="eazy-summary__item">
                <div class="item__icon item__icon--blue">
                    <i data-feather="activity"></i>
                </div>
                <div class="item__text">
                    <span>Jumlah Piutang Keseluruhan</span>
                    <span id="total_piutang">Rp 100,000,000,00</span>
                </div>
            </div>
            <div class="eazy-summary__item">
                <div class="item__icon item__icon--green">
                    <i data-feather="credit-card"></i>
                </div>
                <div class="item__text">
                    <span>Jumlah Piutang Terbayar</span>
                    <span id="piutang_terbayar">Rp 50,000,000,00</span>
                </div>
            </div>
            <div class="eazy-summary__item">
                <div class="item__icon item__icon--red">
                    <i data-feather="percent"></i>
                </div>
                <div class="item__text">
                    <span>Total Sisa Tagihan Keseluruhan</span>
                    <span id="sisa_piutang">Rp 50,000,000,00</span>
                </div>
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
                    dataSrc: function(json){
                        var data = [];
                        json.data.forEach(row => {
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
                                total_piutang += total_harus_bayar - total_terbayar;

                                if (row.student[i].payment.prr_total - row.student[i].payment.prr_paid > 0) {
                                    total_mahasiswa_belum_lunas++;
                                } else {
                                    total_mahasiswa_lunas++;
                                }
                            }
                            all_total_piutang += total_piutang;
                            if(all_total_piutang > 0){
                                data.push(row);
                            }
                        })
                        
                        for(var i = 1; i <= 7; i++){
                            colsfoot[i].innerHTML = '0'
                        }
                        $('#total_piutang').html('0');
                        $('#piutang_terbayar').html('0');
                        $('#sisa_piutang').html('0');

                        json.data = data;
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
                                link: _baseURL + '/report/new-student-receivables/program-study/' + row.studyprogram_id
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
                                total_piutang += total_harus_bayar - total_terbayar;

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
                            $('#total_piutang').html(this.template.currencyCell(all_total_harus_bayar));
                            colsfoot[5].innerHTML = this.template.currencyCell(all_total_harus_bayar);
                            all_total_terbayar += total_terbayar;
                            $('#piutang_terbayar').html(this.template.currencyCell(all_total_terbayar));
                            colsfoot[6].innerHTML = this.template.currencyCell(all_total_terbayar);
                            all_total_piutang += total_piutang;
                            $('#sisa_piutang').html(this.template.currencyCell(all_total_piutang));
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
</script>
@endsection