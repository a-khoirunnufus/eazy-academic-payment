@extends('layouts.static_master')

@section('page_title', 'Contoh Log Queue')
@section('sidebar-size', 'collapsed')
@section('url_back', '')

@section('content')

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mainModal">
        Log Generate
    </button>

    <div class="modal fade" id="mainModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white" style="border: unset; padding: 2rem 3rem 3rem 3rem">
                    <h4 class="modal-title fw-bolder" id="mainModalLabel">Log Generate</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 pt-0">

                    <div class="d-flex flex-row justify-content-between mb-3" style="gap: 1rem">
                        <div>
                            <small class="text-secondary">Periode Tagihan</small><br>
                            <span>2022/2023 - Ganjil</span>
                        </div>
                        <div>
                            <small class="text-secondary">Universitas</small><br>
                            <span>Universitas Telkom</span>
                        </div>
                        <div>
                            <small class="text-secondary">Fakultas</small><br>
                            <span>Fakultas Informatika</span>
                        </div>
                        <div>
                            <small class="text-secondary">Program Studi</small><br>
                            <span>S1 Informatika</span>
                        </div>
                    </div>

                    <div class="accordion border" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <div class="d-flex flex-column" style="gap: 1rem">
                                        <div>Proses Generate Invoice</div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body p-0">
                                    <ul class="list-group eazy-queue-list">
                                        {{-- Loop 10 times --}}
                                        @foreach(array_fill(0, 10, 0) as $item)
                                            <li class="list-group-item">
                                                <div class="queue-item d-flex justify-content-between">
                                                    <div>
                                                        <div class="d-flex flex-row">
                                                            <span class="d-inline-block me-1">Jusuf Kalla - 483192</span>
                                                            <div class="btn-toggle-queue-item" style="line-height: 1rem; cursor: pointer;">
                                                                <i data-feather="eye"></i>
                                                            </div>
                                                        </div>
                                                        <div class="queue-item__detail text-secondary" style="margin-top: .5rem; font-size: .9rem">
                                                            <span>Tahun Masuk: 2022/2023</span><br>
                                                            <span>Jenis Perkuliahan: Reguler<span><br>
                                                            <span>Periode Masuk: Periode April 1</span><br>
                                                            <span>Jalur Masuk: USM</span>
                                                        </div>
                                                    </div>
                                                    <div><span class="badge bg-success">Selesai</span></div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="queue-item d-flex justify-content-between">
                                                    <div>
                                                        <div class="d-flex flex-row">
                                                            <span class="d-inline-block me-1">Ghina Nelaputri - 467170</span>
                                                            <div class="btn-toggle-queue-item" style="line-height: 1rem; cursor: pointer;">
                                                                <i data-feather="eye"></i>
                                                            </div>
                                                        </div>
                                                        <div class="queue-item__detail text-secondary" style="margin-top: .5rem; font-size: .9rem">
                                                            <span>Tahun Masuk: 2022/2023</span><br>
                                                            <span>Jenis Perkuliahan: Reguler<span><br>
                                                            <span>Periode Masuk: Periode April 1</span><br>
                                                            <span>Jalur Masuk: USM</span>
                                                        </div>
                                                    </div>
                                                    <div><span class="badge bg-danger">Gagal</span></div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="queue-item d-flex justify-content-between">
                                                    <div>
                                                        <div class="d-flex flex-row">
                                                            <span class="d-inline-block me-1">Hanadia Qisti - 439163</span>
                                                            <div class="btn-toggle-queue-item" style="line-height: 1rem; cursor: pointer;">
                                                                <i data-feather="eye"></i>
                                                            </div>
                                                        </div>
                                                        <div class="queue-item__detail text-secondary" style="margin-top: .5rem; font-size: .9rem">
                                                            <span>Tahun Masuk: 2022/2023</span><br>
                                                            <span>Jenis Perkuliahan: Reguler<span><br>
                                                            <span>Periode Masuk: Periode April 1</span><br>
                                                            <span>Jalur Masuk: USM</span>
                                                        </div>
                                                    </div>
                                                    <div><span class="badge bg-primary">Dalam Proses</span></div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js_section')

    <script>
        $(function () {

            // enable queue item toggle detail
            $('.btn-toggle-queue-item').click(function() {
                $(this).parents('.queue-item').toggleClass('show-detail');
            });

        });
    </script>

@endsection
