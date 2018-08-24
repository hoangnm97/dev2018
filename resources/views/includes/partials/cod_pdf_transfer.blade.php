<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta name="generator" content="pdf2htmlEX"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
        width: 700px;
        margin:  0 auto;
        background: #fff;
        padding: 0 75px;
    }

    .box-contact{
        position: absolute;
        top: 185px;
        left: 423px;
    }

    .box-contact .sp{
        margin: 0;
        font-size: 14px;
        line-height: 14px;
        color: #696969;
    }

    .bg-welcome{
        height: 1088px;
        width: 100%;
        position: relative;
    }

    .bg_luoi{
        position: absolute;
        background: url("https://hocexcel.online/default/hocexcel/pdf_icon/luoi.png");
        background-repeat: no-repeat;
        top: 220px;
        left: 165px;
        height: 250px;
        width: 532px;
    }

    .user-contact p{
        margin: 0;
        line-height: 12px;
        font-size: 12px;
        color: #333;
        font-style: italic;
    }

    .cod_address p{
        margin: 0;
        line-height: 12px;
        color: #777;
        font-size: 11px;
    }

    .logo{
        text-align: right;
        padding: 37px 0;
    }
    .logo img{
        width: 305px;
        padding-right: 53px;
    }

    .box_address{
        border: 1px solid #979797;
        border-radius: 10px;
        width: 290px;
        padding: 10px 15px;
        height: 147px;
    }
    .no-margin{
        margin: 0;
    }
    .text-welcome{
        color: #7d7d7d;
    }
    .row_box{
        padding: 40px 25px 0 25px;
    }

    .row_box p{
        line-height: 18px;
        font-size: 14px;
        color: #7d7d7d;
    }

    .course_name{
        margin: 5px 0 45px 0;
        font-size: 25px;
        color: #333 !important;
    }

    .input-code{
        width: 262px;
        border: 2px solid #979797;
        display: inline-block;
        margin-left: 85px;
        text-align: center;
        line-height: 18px;
        font-size: 20px;
        color: #7d7d7d;
        font-weight: bold;
        padding: 13px 0;
    }

    .gui_active{
        text-align: center;
        margin-top: 0;
        margin-bottom: 0;
    }
    .stt{
        display: inline-block;
        width: 30px;
        height: 30px;
        border-radius: 100%;
        border: 1px solid #979797;
        text-align: center;
        position: absolute;
        left: 0;
        top: -3px;
        font-size: 12px;
    }
    .chu-ky img{
        width: 120px;
    }
    .line-break{
        position: absolute;
        width: 34px;
        height: 2px;
        background: #ccc5c5;
    }
    .line-left-top,.line-left-bottom{
        left: -50px;
    }

    .line-right-top,.line-right-bottom{
        right: 0;
    }
    .line-left-top, .line-right-top{
        top: 395px;
    }

    .line-left-bottom, .line-right-bottom{
        top: 790px;
    }
</style>

<div class="wrap">
    <div class="wrap_padding">
        <div style="page-break-inside:avoid;"></div>
        <div class="bg-welcome" style="position: relative">
            <div style="page-break-inside:avoid;"></div>
            <div class="logo">
                <img src="https://hocexcel.online/default/hocexcel/pdf_icon/logo_01.png">
            </div>
            <div class="bg_luoi"></div>
            <div class="box_address" style="position: relative">
                <p style="margin: 0;position: absolute;">
                    <img style="width: 92px" src="https://hocexcel.online/default/hocexcel/pdf_icon/logo_01.png">
                </p>
                <div class="cod_address" style="padding-left: 100px;">
                    <p>102 Thái Thịnh, Đống Đa, Hà Nội</p>
                    <p>Hotline: 097.797.2223</p>
                </div>
                <div class="user-contact" style="margin-top: 15px;">
                    <p class="sp">{{ $contact->contact_name }}</p>
                    <p class="sp">{{ $contact->contact_address }}</p>
                    <p class="sp">Số điện thoại: {{ $contact->contact_phone }}</p>
                </div>
            </div>

            <div class="row_box">
                <p class="no-margin text-welcome" style="margin-bottom: 2px;">Chào mừng <b>{{ $contact->contact_name }}</b></p>
                <p class="no-margin text-welcome">đã đến với khóa học / ebook</p>
                <p class="no-margin course_name">{{ $contact->combo_title }}</p>

                <p class="no-margin" style="margin-top: 85px;margin-bottom: 10px">
                    <span style="vertical-align: top;">mã kích hoạt </span>
                    <span class="input-code">{{ $contact->code }}</span>
                </p>

                <p class="no-margin" style="position: relative">
                    <span class="stt">1</span>
                    <span style="padding-left: 50px;">Truy cập địa chỉ</span>
                </p>
                <p class="gui_active">
                    <img style="height: 240px" src="https://hocexcel.online/default/hocexcel/pdf_icon/gui_active.png">
                </p>
                <p class="no-margin" style="position: relative">
                    <span class="stt">2</span>
                    <span style="padding-left: 50px;">Làm theo hướng dẫn</span>
                </p>
                <p class="no-margin" style="margin-top:10px;">Học Excel là nền tảng giáo dục trực tuyến với các khóa học giúp bạn làm việc hiệu quả hơn.</p>
                <p class="no-margin">Tôi là Thanh Nguyễn, Cảm ơn bạn đã sử dụng dịch vụ của Học Excel Online !</p>
                <p class="no-margin">Các khóa học trực tuyến https://hocexcel.online/</p>
                <p class="chu-ky no-margin">
                    <img src="https://hocexcel.online/default/hocexcel/pdf_icon/chu_ky.png">
                </p>
                <p class="no-margin">Nguyễn Đức Thanh</p>
                <p class="no-margin">Founder của Học Excel Online</p>
            </div>

            <div class="box-contact">
                <p class="sp">Học Excel Online</p>
                <p class="sp">Website: https://hocexcel.online</p>
                <p class="sp">Hotline: 097.797.2223</p>
                <p class="sp">Email: listen@hocexcel.online</p>
                <p class="sp">Địa chỉ:</p>
                <p class="sp">102 Thái Thịnh, Đống Đa,Hà Nội</p>
            </div>
            <div class="line-break line-left-top"></div>
            <div class="line-break line-right-top"></div>
            <div class="line-break line-left-bottom"></div>
            <div class="line-break line-right-bottom"></div>
        </div>
    </div>
</div>
</body>
</html>
