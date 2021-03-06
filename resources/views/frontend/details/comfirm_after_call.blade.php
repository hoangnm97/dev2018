@extends('.frontend.layouts.defaut')

@section('style')
    <style>
        .comfirm-after-call {
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
        .ask {
            height: 50px;
            line-height: 50px;
            display: flex;
            justify-content: space-between;

        }
        .ask span.ask-title {
            padding-left: 14px;
            color: #888888;
        }

        .row-data span:first-child {
            color: #888888;
            font-size: 0.7em;
        }
        .row-data span:last-child {
            color: #347AF7;
        }

        .card-action i {
            font-size: 2.5em;
            margin-top: 20px;
            margin-bottom: 20px;
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
        .answer{
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-areas:
                    "yes no";
            padding-bottom: 10px;

        }

        .answer-yes{
            grid-area: yes;
            text-align: center;
        }
        .answer-no{
            grid-area: no;
            text-align: center;
        }

        .answer-yes .button{
            border: 1px solid #30A64A;
            padding: 5px 20px;
            color: #30A64A;
        }

        .answer-no .button{
            border: 1px solid #FF7C7C;
            padding: 5px 20px;
            color: #FF7C7C;
        }

        .button{
            background: white;
            border: 1px solid #347AF7;
            padding: 5px 0;
            font-size: 15px;
            border-radius: 2px;
            outline: none;
        }

        .button i{
            margin-right: 5px;
        }

        .answer-no .button-no-selected{
            background: #FF7C7C;
            color: white;
        }

        .answer-yes .button-yes-selected{
            background: #30A64A;
            color: white;
        }

        .no-reply-call, .yes-reply-call{
            margin: 10px;
            background: #FFFFFF;
            box-shadow: 0px 1px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding-bottom: 10px;
        }

        .work-need{
            padding-top: 20px;
            display: grid;
            grid-template-columns: 1fr;
            margin-left: 17px;
            margin-right: 17px;
        }
        .work-need span{
            font-size: 15px;
            color: #888888;
        }

        .work-need .select-work{
            border: 1px solid #C4C4C4;
            padding: 10px 10px;
            margin-top: 10px;
            font-size: 15px;
            color: #C4C4C4;
            border-radius: 2px;
            background: white;
        }

        .work-need .select-work i{
            margin: 0 5px;
        }
        .icon-right{
            float: right;
        }

        .work-need .interactive-note{
            border: 1px solid #C4C4C4;
            padding: 10px 10px;
            margin-top: 10px;
            font-size: 14px;
            color: #C4C4C4;
            border-radius: 2px;
            background: white;
            outline: none;
        }
        .save-work{
            display: grid;
            grid-template-columns: 1fr;
            grid-template-areas:
                    "save";
            margin-top: 20px;
        }

        .button-save{
            color: #347AF7;
            grid-area: save;
            margin: 0 17px;
        }

        .status-lead{
            margin: 0 17px;
            padding-top: 20px;
        }

        .status-lead span{
            padding-bottom: 10px;
            color: #888888;
            font-size: 15px;
        }

        .button-status{
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(3,1fr);
            grid-template-areas:
                    "td cal not"
                    "can lo wi";
        }
        .button-todo{
            grid-area: td;
            border: 1px solid #FF7C7C;
            color: #FF7C7C;
            margin: 8px 10px;

        }
        .button-todo-active{
            grid-area: td;
            background: #FF7C7C;
            border: 1px solid #FF7C7C;
            color: white;
            margin: 8px 10px;

        }

        .button-defaut-status{
            margin: 8px 10px;
        }

        .button-not{
            grid-area: not;
        }
        .button-calender{
            grid-area: cal;
            color: #347AF7;
        }

        .button-calender-active{
            grid-area: cal;
            color: white;
            background: #347AF7;
        }


        .button-cancel{
            grid-area: can;
            color: #C4C4C4;
            border: 1px solid #C4C4C4;
        }

        .button-lost{
            grid-area: lo;
            color: #888888;
            border: 1px solid #888888;
        }

        .button-win{
            grid-area: wi;
            color: #30A64A;
            border: 1px solid #30A64A;
        }

        .hide{
            display: none!important;
        }

        .work-need .select-work-active{
            color: #347AF7;
            border: 1px solid #347AF7;;
        }

        .steps{
            list-style-type: none;
            border: 1px solid #347AF7;
            color: #347AF7;
            padding: 10px;

        }

        .steps i{
            margin-right: 10px;
        }
        .steps-active{
            background: #347AF7;
            color: white;
        }

        .setting-timestamp{
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(2,1fr);
            grid-template-areas:
                    "day time"
        }

        .setting-day{
            margin-right: 20px;
        }

        .setting-time{
            grid-area: time;
            margin-left: 20px;
        }

        .button-timestamp{
            color: #347AF7;
            border: 1px solid #347AF7;
            justify-content: space-between;
        }
        .select-list-date{
            float: left;
            border: 1px solid #347AF7;
            margin: 0 5px;
            padding: 5px 5px;
        }


        .setting-date{
            grid-area: day;
            margin-right: 20px;
            display: grid;
            grid-template-columns: repeat(3,1fr);
            grid-template-areas:
                    "select-day select-month select-year "
        }
        .select-list-day{
            grid-area: select-day;
        }
        .select-list-month{
            grid-area: select-month;
        }
        .select-list-year{
            grid-area: select-year;
        }

        textarea::placeholder{
            color: #C4C4C4;
        }

    </style>

@endsection

@section('after-scripts-end')
    <script>

        $('.click-button-yes').on('click',function(){

            if($('.no-reply-call').hasClass('show')){
                $('.no-reply-call').removeClass('show');
                $('.no-reply-call').addClass('hide');
                $('.click-button-no').removeClass('button-no-selected')
            }
            $('.yes-reply-call').removeClass('hide');
            $('.yes-reply-call').addClass('show');
            $(this).addClass('button-yes-selected');
            $('.button-calender').removeClass('button-calender-active');
            $('.button-todo').addClass('button-todo-active');
            $('.click-calender').addClass('hide');
            $('.click-calender').removeClass('show');
        });

        $('.click-button-no').on('click',function(){

            if($('.yes-reply-call').hasClass('show')){
                $('.yes-reply-call').removeClass('show');
                $('.yes-reply-call').addClass('hide');
                $('.click-button-yes').removeClass('button-yes-selected')
            }
            $('.no-reply-call').removeClass('hide');
            $('.no-reply-call').addClass('show');
            $(this).addClass('button-no-selected');
            $('.button-calender').removeClass('button-calender-active');
            $('.button-todo').addClass('button-todo-active');
        });

        $('.button-defaut-status').on('click',function(){
            $('.button-defaut-status').removeClass('')
        })

        $('.selected-list').on('click',function(){

            var scroll_to = $('div.main').scrollTop() + $(this).offset().top - $('div.main').offset().top;

            $('div.main').animate({
                scrollTop: scroll_to - 20
            });
            if($(this).parent('.work-need').find('.selected-steps').hasClass('show')){
                $(this).parent('.work-need').find('.selected-steps').addClass('hide');
                $(this).parent('.work-need').find('.selected-steps').removeClass('show');
            }
            else{
                $(this).parent('.work-need').find('.selected-steps').removeClass('hide');
                $(this).parent('.work-need').find('.selected-steps').addClass('show');
            }

        })

        $('.click-selected-calender').on('click',function(){
            $('.yes-reply-call').removeClass('show');
            $('.yes-reply-call').addClass('hide');
            $('.click-calender').removeClass('hide');
            $('.click-calender').addClass('show');
            $('.button-calender').addClass('button-calender-active');
            $('.button-todo').removeClass('button-todo-active');

        });

        //    $('.setting-day').on('click',function(){
        //        $(this).addClass('hide');
        //        $('.setting-date').removeClass('hide');
        //    })
        //
        //    $('.select-list-day').on('click',function(){
        //
        //    })
    </script>
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

        <!-- Xác nhận khách hàng có nhấc máy không -->
        <div class="comfirm-after-call">
            <div class="ask">
                <span class="ask-title">Khách hàng có nhấc máy không?</span>
            </div>
            <div class="answer">
                <div class="answer-yes">
                    <button class="button click-button-yes" >
                        <i class="fas fa-phone"></i>
                        CÓ
                    </button>
                </div>
                <div class="answer-no">

                    <button class="button click-button-no">
                        <i class="fas fa-phone"></i>
                        KHÔNG
                    </button>
                </div>
            </div>
        </div>

        <!-- Không nhấc máy -->
        <div class="no-reply-call hide">
            <!-- Trạng thái lead -->
            <div class="status-lead">
                <span>Trạng thái lead</span>
                <div class="button-status">
                    <button class="button button-defaut-status button-todo ">
                        <i class="fas fa-exclamation"></i>
                        Todo
                    </button>

                    <button class="button button-defaut-status button-calender">
                        <i class="fas fa-clock"></i>
                        Lịch
                    </button>

                    <button class="button button-defaut-status button-cancel">
                        <i class="far fa-meh"></i>
                        Cancel
                    </button>

                    <button class="button button-defaut-status button-lost">
                        <i class="far fa-frown"></i>
                        Lost
                    </button>

                    <button class="button button-defaut-status button-win">
                        <i class="fas fa-trophy"></i>
                        Win
                    </button>
                </div>

            </div>

            <div class="work-need">
                <span>Việc lần sau sẽ làm</span>
                @include('.frontend.includes.work-need')
            </div>

            <div class="work-need">
                <span>Ghi chú cho tương tác này</span>
                <textarea class="interactive-note" rows="2">Ghi chú</textarea>
            </div>

            <div class="save-work">
                <button class="button button-save">LƯU</button>
            </div>
        </div>


        <!-- Có nhấc máy-->
        <div class="yes-reply-call hide">
            <!-- Chuyển steps-->
            <div class="work-need ">
                <span>Chuyển steps</span>
                @include('.frontend.includes.list-steps')
            </div>

            <!-- Trạng thái lead -->
            <div class="status-lead">
                <span>Trạng thái lead</span>
                <div class="button-status">
                    <button class="button button-defaut-status button-todo button-todo-active">
                        <i class="fas fa-exclamation"></i>
                        Todo
                    </button>

                    <button class="button button-defaut-status button-calender click-selected-calender">
                        <i class="fas fa-clock"></i>
                        Lịch
                    </button>

                    <button class="button button-defaut-status button-cancel">
                        <i class="far fa-meh"></i>
                        Cancel
                    </button>

                    <button class="button button-defaut-status button-lost">
                        <i class="far fa-frown"></i>
                        Lost
                    </button>

                    <button class="button button-defaut-status button-win">
                        <i class="fas fa-trophy"></i>
                        Win
                    </button>
                </div>

            </div>
            <div class="work-need">
                <span>Việc lần sau sẽ làm</span>
                @include('.frontend.includes.work-need')

            </div>

            <div class="work-need">
                <span>Ghi chú cho tương tác này</span>
                <textarea class="interactive-note" rows="2">Ghi chú</textarea>
            </div>

            <div class="save-work">
                <button class="button button-save">LƯU</button>
            </div>
        </div>

        <!-- Đặt lịch -->
        <div class="yes-reply-call click-calender hide">
            <!-- Chuyển steps-->
            <div class="work-need ">
                <span>Chuyển steps</span>
                <div class="select-work select-work-active selected-list">
                    <i class="fas fa-caret-down icon-right"></i>
                    S2 - Đang liên hệ
                </div>

                <ul class="selected-steps hide">
                    <li class="steps">S0 - Lead mới</li>
                    <li class="steps">  S1 - Phân lead</li>
                    <li class="steps steps-active">S2 - Đang liên hệ</li>
                    <li class="steps">S3 - Hẹn gặp</li>
                    <li class="steps">S4 - Xem nhà</li>
                    <li class="steps">S5 - Mua nhà</li>
                </ul>
            </div>

            <!-- Trạng thái lead -->
            <div class="status-lead">
                <span>Trạng thái lead</span>
                <div class="button-status">
                    <button class="button button-defaut-status button-todo ">
                        <i class="fas fa-exclamation"></i>
                        Todo
                    </button>

                    <button class="button button-defaut-status button-calender">
                        <i class="fas fa-clock"></i>
                        Lịch
                    </button>

                </div>

                <div class="setting-timestamp">
                    <button class="button button-timestamp setting-day"> 20.08.2018 </button>
                    {{--<div class="setting-date hide">--}}
                    {{--<div class="select-list-date select-list-day">20</div>--}}
                    {{--<div class="select-list-date select-list-month">10</div>--}}
                    {{--<div class="select-list-date select-list-year">2018</div>--}}
                    {{--</div>--}}
                    <button class="button button-timestamp setting-time"> 14:00</button>
                </div>

            </div>

            <div></div>

            <div class="work-need">
                <span>Việc lần sau sẽ làm</span>
                <div class="select-work">
                    <i class="fas fa-caret-down icon-right"></i>
                    <i class="fas fa-phone"></i> Gọi điện
                </div>
            </div>

            <div class="work-need">
                <span>Ghi chú cho tương tác này</span>
                <textarea class="interactive-note" rows="2" placeholder="Ghi chú"></textarea>
            </div>

            <div class="save-work">
                <button class="button button-save">LƯU</button>
            </div>

        </div>

    </div>

    @include('.frontend.includes.footer')

@endsection