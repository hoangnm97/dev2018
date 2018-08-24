@extends('.frontend.layouts.defaut')


@section('style')
    <style>
        .fa-exclamation-circle {
            color: #FF7C7C;
            padding-right: 7px;
        }
        .fa-headset {
            color: #DADADA;
            padding-right: 7px;
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
        .card-action i {
            font-size: 2.5em;
            margin-top: 20px;
            margin-bottom: 20px;
        }
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

        .explain-lead {
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

        .explain-lead-share {
            grid-area: ta;
            margin-left: 17px;
            text-align: left;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .ta-explain-share{
            margin-left: 10px;
            text-align: left;
            font-size: 0.7em;
            color: #888888;
        }

        .ta-explain-share span{
            color: #347AF7;
            font-weight: 700;

        }

        .explain-lead .sales-step{
            margin-bottom: 10px;
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

                <div> ... </div>
            </div>
        </div>

        <!-- explain -->

        <div class="section explain-section">
            <div class="section-body">
                <div class="explain-lead">
                    <div class="explain-lead-share">
                        <span class="ta-sales">Manager</span>
                        <span class="ta-explain-share">Phân lead cho <span>Telesale A</span></span>
                    </div>
                    <div class="sales-status"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="interaction-timestamp"><span>12.08.2018 · 09:00</span></div>
                    <div class="sales-step"><span>S1 - Phân lead</span></div>
                </div>

                <div class="explain-lead">
                    <div class="explain-lead-share">
                        <span class="ta-sales">Khách hàng</span>
                        <span class="ta-explain-share">Đăng ký</span>
                    </div>
                    <div class="interaction-timestamp"><span>12.08.2018 · 09:00</span></div>
                    <div class="sales-step"><span>S0 - Lead mới</span></div>
                </div>
            </div>
        </div>

    </div>
    @include('.frontend.includes.footer')

@endsection