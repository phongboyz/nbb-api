<!DOCTYPE html>
<html>
<head>
    <title>{{__('lang.welcome')}}</title>
    <style>
        body * {
            font-family: 'Phetsarath OT'
        }
    </style>
</head>
<body> 
    <h2>ແຈ້ງເຕືອນຂໍ້ຄວາມສົ່ງເຖິງທ່ານໃນລະບົບເກັບກຳເອກະສານ:</h2>
    <p>ຜູ້ສົ່ງ: {{$details['username']}} 
    <br>ວັນທີ: {{$details['date']}} 
    <br> ຫົວຂໍ້: <u>{{ $details['subject']  }}</u> 
    <br>ເນື້ອໃນ: <u>{!! $details['note']  !!}</u></p>
    <h3><b>ລິ້ງເວບໄຊ: <u>https://web.nbb.com.la/inbox</u></b></h3>
    <p>-------------------------------------</p> 
    <p style="line-height:5px">ສອບຖາມຂໍ້ມູນເພີ່ມຕື່ມ :</p> 
    <p style="line-height:5px">ໂທ : 021 264407-21</p> 
    <p style="line-height:5px">ເວບໄຊ : www.nbb.com.la</p> 
    <p style="line-height:5px">FB Fanpage : https://www.facebook.com/nbb.com.la</p>
</body>
</html>