<div>
    <h3>Set Virtual Account Expire</h3>

    @if (session()->has('dev-test.success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success !</strong> {{ session('dev-test.success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('dev-test.error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error !</strong> {{ session('dev-test.error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mt-3">
        <form id="form-regenerate-va" action="{{ url('api/payment/dev-test/regenerate-va') }}" method="post">
            @csrf
            <div class="mb-3">
                <label class="form-label">Kode Cicilan</label>
                <input type="number" class="form-control" name="bill_id" />
                @if($errors->has('bill_id'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('bill_id') }}</strong>
                    </div>
                @endif
            </div>
            <div class="mb-3">
                <label class="form-label">Waktu Kadaluarsa Virtual Account</label>
                <input type="text" class="form-control" name="new_va_exp_time" />
                @if($errors->has('new_va_exp_time'))
                    <div class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('new_va_exp_time') }}</strong>
                    </div>
                @endif
            </div>
            <div>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        $("#form-regenerate-va input[name=new_va_exp_time]").flatpickr({
            dateFormat: 'Y-m-d H:i:s +07:00',
            enableTime: true,
            time_24hr: true,
        });
    </script>
@endpush

