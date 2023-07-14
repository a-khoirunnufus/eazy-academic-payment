<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Simulator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  </head>
  <body>
    <div class="container py-3">
        <h2 class="mb-3">Payment Simulator BCA</h2>
        <form action="{{ url('api/payment-simulator/pay') }}" method="post">
            @csrf
            <input type="hidden" name="payment_method" value="bca_va">
            <div class="mb-3">
                <label class="form-label">Nomor Virtual Account</label>
                <input type="number" name="va_number" class="form-control" />
            </div>
            <div class="mb-3">
                <label class="form-label">Nominal Pembayaran</label>
                <input type="number" name="payment_nominal" class="form-control" />
            </div>
            <div>
                <button class="btn btn-success" type="submit">Bayar</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
