<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="{{ url('api/payment/settings/paymentrates/upload-file-for-import') }}" method="post" enctype='multipart/form-data'>
        @csrf
        <input name="file" type="file" />
        <button type="submit">Upload</button>
    </form>
    <div>
        {{ json_encode($errors) }}
    </div>
</body>
</html>
