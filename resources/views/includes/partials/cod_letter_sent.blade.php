<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta name="generator" content="pdf2htmlEX"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <style>
        body,html{
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <style class="shared-css" type="text/css" >
        body {
            font-family: DejaVu Sans;
        }
        .wrap{
            width: 800px;
            margin:  0 auto;
            background: #fff;
        }
        .wh_100{
            width: 100%;
            display: block;
            overflow: hidden;
        }
        .box-logo{
            width: 100%;
            display: block;
        }
        .logo{
            display: block;
            text-align: center;
        }
        .box-contact{
            border: 2px solid #979797;
            border-radius: 10px;
            padding: 20px;
        }
        .wrap_padding{
            padding: 20px;
        }

        .box-contact .sp{
            margin: 0;
            font-size: 30px;
            line-height: 35px;
            color: #696969;
        }
        .box-contact .logo-small{
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin: 0 0 10px 0 !important;
        }
        .page-break {
            page-break-after: always;
        }
        .bg-welcome{
            background-image: url("https://hocexcel.online/default/hocexcel/pdf_icon/bg_welcome_01.png");
            background-repeat: no-repeat;
            height: 950px;
            width: 100%;
            margin-top: 50px;
        }
    </style>

    <div class="wrap">
        <div class="wrap_padding">
            <div class="box-logo" style="width: 100%;margin-bottom: 30px;display: block">
                <div class="logo">
                    <img src="https://hocexcel.online/default/hocexcel/pdf_icon/logo_01.png">
                </div>
            </div>
            <div class="wh_100">

                <div class="box-contact" style="margin-bottom: 30px">
                    <p class="sp">Học Excel Online</p>
                    <p class="sp">Website: https://hocexcel.online</p>
                    <p class="sp">Hotline: 098.888.8888</p>
                    <p class="sp">Email: listen@hocexcel.online</p>
                    <p class="sp">Địa chỉ:</p>
                    <p class="sp">102 Thái Thịnh, Đống Đa,Hà Nội</p>
                </div>

                <div class="box-contact" style="">
                    <p class="logo-small">
                        <img style="width: 210px" src="https://hocexcel.online/default/hocexcel/pdf_icon/logo_01.png">
                    </p>
                    <p class="sp" style="color: #333">Người nhận:</p>
                    <p class="sp">{{ $contact->contact_name }}</p>
                    <p class="sp">{{ $contact->contact_address }}</p>
                    <p class="sp">Số điện thoại: {{ $contact->contact_phone }}</p>
                </div>

            </div>
            <div class="page-break"></div>
            <div class="bg-welcome" style="padding: 20px"></div>
        </div>
    </div>
</body>
</html>
