@extends('.frontend.layouts.defaut')

@section('style')
    <style>
        .card-item {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 5px;
            margin: 10px;
            padding: 10px;
            background: #FFFFFF;
            box-shadow: 0px 1px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .card-info:nth-child(2n) {
            text-align: right;
        }
        .card-info-name {
            color: #347AF7;
            font-weight: 500;
            padding-left: 7px;
        }
        .card-info-steps span,
        .card-info-last-activity span {
            color: #888888;
            padding: 3px 7px;
            background: #EEEEEE;
            border-radius: 5px;
            font-size: 0.8em;
        }
        .card-info-created-date span {
            color: #888888;
            font-size: 0.8em;
            padding-left: 7px;
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
        .fa-sort-alpha-up {
            color: #347AF7;
        }
        .header-title {
            text-align: left;
            padding-left: 27px;
            font-weight: 700;
            color: #888888;
        }
        .open-pagination{
            color: #347AF7;
        }
        .pagination{
            margin: 20px 25px;
        }
        .pagination i{
            color: #DADADA;
            float: right;
        }
        .button{
            background: white;
            border: 1px solid #347AF7;
            padding: 5px 0;
            font-size: 15px;
            border-radius: 2px;
            outline: none;
            color: #347AF7;

        }
        .pagination .button i{
            color: #347AF7;
            float: none;
        }


        .input-pagination{
            width: 60px;
            height: 30px;
            border: 1px solid #347AF7;
            text-align: center;
            color: #347AF7;
            font-size: 14px;
        }

        .arow-pagination button{
            width: 30px;
            height: 30px;
        }

        .list-pagination{
            display: grid;
            grid-template-rows: repeat(3,40px);
            /*text-align: center;*/
        }

        .button-lead-all{
            width: 60px;
            height: 30px;
        }
        .arow-pagination{
            margin-top: auto;
            margin-bottom: auto;
            display: grid;
            grid-template-columns: repeat(2,40px) 70px repeat(2,40px);
        }
        .list-pagination span {
            margin-top: auto;
            margin-bottom: auto;
            color: #888888;
            font-size: 0.8em;
        }

    </style>

@endsection

@section('after-scripts-end')
    <script>
        $('.open-pagination').on('click',function(){
            $('.pagination').removeClass('hide');
        })

        $('.close-pagination').on('click',function(){
            $('.pagination').addClass('hide');
        })
    </script>
@endsection

@section('content')
    <header class="header">
        <div class="header-title">All leads</div>
        <div class="header-btn">
            <i class="fas fa-sort-alpha-up"></i>
        </div>
    </header>
    <div class="main">
        <div class="lead-count-info"><span>Hiển thị <span class="open-pagination">10 leads</span> trên tổng số 2340 leads</span></div>

        <div class="pagination hide">
            <i class="fas fa-times close-pagination"></i>
            <div class="list-pagination">
                <button class="button button-lead-all">50</button>
                <span>Đi tới trang</span>
                <div class="arow-pagination">
                    <button class="button"><i class="fas fa-angle-double-left"></i></button>
                    <button class="button"><i class="fas fa-angle-left"></i></button>
                    <input value="1" class="input-pagination"/>
                    <button class="button"><i class="fas fa-angle-right"></i></button>
                    <button class="button"><i class="fas fa-angle-double-right"></i></button>
                </div>
            </div>

        </div>
        <a href="detail">
            <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 0</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
            </div>
        </a>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 1</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 2</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 3</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 4</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 5</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 6</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 7</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 8</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 9</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
        <div class="card-item">
            <div class="card-info card-info-name">Nguyễn Xuân Bách 10</div>
            <div class="card-info card-info-status"><i class="fas fa-exclamation-circle"></i></div>
            <div class="card-info card-info-steps"><span>S2 - Đang liên hệ</span></div>
            <div class="card-info card-info-last-activity"><span>1 ngày</span></div>
            <div class="card-info card-info-created-date"><span>10.08.2018</span></div>
            <div class="card-info card-info-next-activity"><i class="fas fa-headset"></i></div>
        </div>
    </div>
    @include('.frontend.includes.footer')
@endsection