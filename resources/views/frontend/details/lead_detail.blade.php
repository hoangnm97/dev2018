@extends('.frontend.layouts.defaut')

@section('style')
    <style>
        .card-item {
            margin: 10px;
            background: #FFFFFF;
            box-shadow: 0px 1px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .fa-exclamation-circle {
            color: #FF7C7C;
            padding-right: 7px;
        }
        .fa-headset {
            color: #DADADA;
            padding-right: 7px;
        }
        .lead-count-info {
            padding: 10px 10px 0 27px;
            color: #888888;
            font-size: 0.8em;
        }
        .header-title {
            text-align: left;
            padding-left: 27px;
            color: #888888;
            height: 60px;
        }
        .header-title span.lead-name {
            font-weight: 700;
            color: #347AF7;
        }
        .header-title span.sales-step {
            color: #888888;
            padding: 3px 7px;
            background: #EEEEEE;
            border-radius: 5px;
            font-size: 0.5em;
        }
        .header-btn {
            padding-right: 10px;
        }
        .card-item-header {
            border-bottom: 1px solid #DADADA;
            height: 50px;
            line-height: 50px;
            display: flex;
            justify-content: space-between;
        }
        .card-item-header span.card-item-header-title {
            padding-left: 17px;
            color: #888888;
        }
        .card-item-header span i.fa-edit {
            padding-right: 17px;
            color: #347AF7;
        }
        .card-item-body {
            display: grid;
            grid-template-columns: 1fr;
        }
        .row-data {
            display: grid;
            grid-template-columns: 1fr 2fr;
            margin-left: 17px;
            margin-right: 17px;
            border-bottom: 1px solid #DADADA;
            height: 40px;
            line-height: 40px;
            vertical-align: middle;
        }
        .row-data:last-child {
            padding-bottom: 10px;
            border-bottom: none;
        }
        .row-data span:first-child {
            color: #888888;
            font-size: 0.7em;
        }
        .row-data span:last-child {
            color: #347AF7;
        }
        .section {
            text-align: center;
            margin-top: 20px;
        }
        .section-title {
            font-weight: 700;
            color: #888888;
        }
        .card-action {
            display: flex;
            justify-content: space-around;
        }
        .card-action i {
            font-size: 2.5em;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .action-email,
        .action-meeting {
            color: #DADADA;
        }
        .action-call {
            color: #30A64A;
        }
        .card-item-history {
            margin: 10px;
            background: #FFFFFF;
            box-shadow: 0px 1px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-areas:
                    "ta ta ta sta"
                    "it it ste ste"
                    "in in in in";
        }
        .telesales-action {
            grid-area: ta;
            margin-left: 17px;
            text-align: left;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .sales-status {
            grid-area: sta;
            text-align: right;
            margin-right: 17px;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .sales-status i.action-meeting {
            color: #347AF7;
        }
        .interaction-timestamp {
            grid-area: it;
            margin-left: 10px;
            text-align: left;
            color: #888888;
        }
        .interaction-timestamp span {
            padding: 3px 7px;
            background: #FFFFFF;
            font-size: 0.7em;
        }
        .sales-step {
            grid-area: ste;
            text-align: right;
            margin-right: 17px;
        }
        .sales-step span {
            color: #888888;
            padding: 3px 7px;
            background: #EEEEEE;
            border-radius: 5px;
            font-size: 0.7em;
        }
        .interaction-note {
            grid-area: in;
            margin-top: 10px;
        }
        .ta-sales {
            text-align: left;
            font-size: 0.7em;
            color: #347AF7;
            font-weight: 700;
        }
        .ta-action {
            margin-left: 10px;
            font-size: 0.7em;
            color: #30A64A;
        }
        .interaction-note {
            text-align: left;
            margin-left: 17px;
            font-size: 0.7em;
            margin-bottom: 10px;
            color: #888888;
        }
    </style>

@endsection

@section('after-scripts-end')

@endsection

@section('content')
    <header class="header">
        <div class="header-title">
            <span class="lead-name">Nguyễn Xuân Bách</span>
            <span class="sales-step">S2 - Đang liên hệ</span>
        </div>
        <div class="header-btn">
            <i class="fas fa-headset"></i>
            <i class="fas fa-exclamation-circle"></i>
        </div>
    </header>
    <div class="main">
        <!-- Thông tin khách hàng -->
        <div class="card-item">
            <div class="card-item-header">
                <span class="card-item-header-title">Thông tin khách hàng</span>
                <span><i class="far fa-edit"></i></span>
            </div>
            <div class="card-item-body">
                <div class="row-data">
                    <span>Họ Tên</span>
                    <span>Nguyễn Xuân Bách</span>
                </div>
                <div class="row-data">
                    <span>Số điện thoại</span>
                    <span>0977972223</span>
                </div>
                <div class="row-data">
                    <span>Địa chỉ</span>
                    <span>Số 02 Lê Văn Lương ...</span>
                </div>
            </div>
        </div>
        <!-- Thông tin sản phẩm -->
        <div class="card-item">
            <div class="card-item-header">
                <span class="card-item-header-title">Thông tin sản phẩm</span>
                <span><i class="far fa-edit"></i></span>
            </div>
            <div class="card-item-body">
                <div class="row-data">
                    <span>Tên SP</span>
                    <span>Nhà Phân Lô</span>
                </div>
                <div class="row-data">
                    <span>Giá trị</span>
                    <span>USD 499.000</span>
                </div>
                <div class="row-data">
                    <span>Thông tin thêm</span>
                    <span>Thông tin thêm về sản phẩm</span>
                </div>
            </div>
        </div>

        <!-- Lịch sử tương tác -->
        <div class="section interactivity-history-section">
            <div class="section-title">
                Lịch sử tương tác >
            </div>
            <div class="section-body">
                <div class="card-item-history">
                    <div class="telesales-action">
                        <span class="ta-sales">Telesales A</span>
                        <span class="ta-action"><i class="action-call fas fa-phone"></i> Nhấc máy</span>
                    </div>
                    <div class="sales-status"><i class="action-meeting far fa-clock"></i></div>
                    <div class="interaction-timestamp"><span>12.08.2018 · 09:00</span></div>
                    <div class="sales-step"><span>S3 - Hẹn gặp</span></div>
                    <div class="interaction-note">Hẹn gặp vào 15.08.2018</div>
                </div>
                <div class="card-item-history">
                    <div class="telesales-action">
                        <span class="ta-sales">Telesales A</span>
                        <span class="ta-action"><i class="action-call fas fa-phone"></i> Nhấc máy</span>
                    </div>
                    <div class="sales-status"><i class="action-meeting far fa-clock"></i></div>
                    <div class="interaction-timestamp"><span>12.08.2018 · 09:00</span></div>
                    <div class="sales-step"><span>S3 - Hẹn gặp</span></div>
                    <div class="interaction-note">Hẹn gặp vào 15.08.2018</div>
                </div>
                <div class="card-item-history">
                    <div class="telesales-action">
                        <span class="ta-sales">Telesales A</span>
                        <span class="ta-action"><i class="action-call fas fa-phone"></i> Nhấc máy</span>
                    </div>
                    <div class="sales-status"><i class="action-meeting far fa-clock"></i></div>
                    <div class="interaction-timestamp"><span>12.08.2018 · 09:00</span></div>
                    <div class="sales-step"><span>S3 - Hẹn gặp</span></div>
                    <div class="interaction-note">Hẹn gặp vào 15.08.2018</div>
                </div>
            </div>
        </div>



        <!-- Action -->

        <div class="section action-section">
            <div class="section-title">
                Action
            </div>
            <div class="card-action card-item">
                <i class="action-email far fa-envelope"></i>
                <i class="action-call fas fa-phone"></i>
                <i class="action-meeting far fa-clock"></i>
            </div>
        </div>

    </div>
    @include('.frontend.includes.footer')

@endsection