@foreach ($log as $item)
    <div class="accordion border">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $item->log_id }}" aria-expanded="false" aria-controls="collapse{{ $item->log_id }}">
                    <div class="d-flex flex-column" style="gap: 1rem">
                        <div>#{{ $item->log_id }} {{ $item->log_activity }}
                        @if ($item->log_status == 1)
                            <span class='badge bg-success'>Selesai</span>
                        @elseif($item->log_status == 3)
                            <span class='badge bg-danger'>Gagal</span>
                        @else
                            <span class='badge bg-primary'>Dalam Proses</span>
                        @endif

                        <br><small class="fst-italic">at {{ $item->updated_at->format('d M Y H:m:s') }} by {{ $item->user->user_fullname }}</small></div>
                    </div>
                </button>
            </h2>
            <div id="collapse{{ $item->log_id }}" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionBody">
                <div class="accordion-body p-0">
                    <ul class="list-group eazy-queue-list" id="list">
                        @foreach ($item->detail as $detail)
                            <li class="list-group-item">
                                <div class="queue-item d-flex justify-content-between">
                                    <div class="border-0">
                                        <div class="d-flex flex-row">
                                            <span class="d-inline-block me-1">{{$detail->lad_title}}</span>
                                        </div>
                                    </div>
                                    <div class="border-0">
                                        @if ($detail->lad_status == 1)
                                            <small class='fst-italic fs-6'>{{ $detail->updated_at->format('d M Y H:m:s') }}</small> <span class='badge bg-success'>Selesai</span>
                                        @elseif($detail->lad_status == 3)
                                            <span class='badge bg-danger'>Gagal</span>
                                        @else
                                            <span class='badge bg-primary'>Dalam Proses</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endforeach
<div class="col-12 mt-2 d-flex justify-content-end">
    {!! $log->withQueryString()->links() !!}
</div>
