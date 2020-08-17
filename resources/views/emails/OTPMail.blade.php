<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie-egde">
        <title>OTP Confirm</title>
    </head>
    <body>
       <h1> Kính gửi Quý khách: {{$name}} </h1>
        <p>Mã xác thực (OTP) của Quý khách đã tạo thành công.</p>
        <br/>
        <p>Mã khách hàng: {{$accountNumber}}</p>
        <br/>
        <p>Mã xác thực OTP của Quý khách là: <h1>{{$OTPCode}}</h1>.</p>
        <p>Quý khách đang thực hiện giao dịch trên  hệ thống Internet Banking của ngân hàng KLXBank.</p>
        <p>Mã xác thực có hiệu lực trong 1 phút kể từ khi Quý khách hàng nhận được email này.</p>
        <br/>
        <p>Cảm ơn Quý khách đã sử dụng dịch vụ của ngân hàng KLXBank.</p>
        <br/>
        <p>Trân trọng</p>
    </body>
</html>