@extends('.frontend.layouts.defaut')


@section('style')
    <style>
        .header-sort{
            background: #FFFFFF;
            text-align: center;
            line-height: 60px;
            border-bottom: 1px solid #EFF0F3;
            box-shadow: 0 0 25px 0 rgba(0, 0, 0, 0.04);
            color: #888888;
        }
        .sort-main{
            margin: 10px;
            background: #FFFFFF;
            box-shadow: 0px 1px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            display: grid;
            padding: 10px 15px;
        }
        .sort-main i{
            text-align: end;
            color: #DADADA;
        }

        .sort-main span{
            color: #888888;
            font-size: 15px;
            margin: 10px 5px 20px 5px;

        }
        .button{
            background: white;
            border: 1px solid #347AF7;
            padding: 5px 0;
            font-size: 15px;
            border-radius: 2px;
            outline: none;
            color: #347AF7;
            margin:0 5px;
            margin-bottom: 10px;

        }

        .sort-name{
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 20px;

        }

        .button-active{
            background: #347AF7;
            color: white;
        }
        .save-sort{
            display: grid;
            grid-template-columns: 1fr;
            margin-top: 80px;
        }

        .filter-inf{
            margin: 20px;
        }
        .filter-inf span{
            color: #777777;
        }

        .inf-fi{
            margin: 10px;
            display: grid;
            grid-template-columns: 1fr;
            grid-template-rows: 1fr 1fr;
        }

        .inf-fi i{
            margin-right: 10px;
        }
        .inf-fi-on{
            margin-top: 5px;
            display: grid;
            grid-template-columns: 1fr 1fr;
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
        .select-work{
            border: 1px solid #C4C4C4;
            padding: 10px 10px;
            margin-top: 10px;
            font-size: 15px;
            color: #C4C4C4;
            border-radius: 2px;
            background: white;
        }

        .select-work i{
            margin: 0 5px;
        }

        .select-work-active{
            color: #347AF7;
            border: 1px solid #347AF7;
        }
        .icon-right{
            float: right;
        }
        .filter-alive{
            display: grid;
            grid-template-columns: 1fr 3fr;
            border: 1px solid #347AF7;
            border-radius: 2px;

        }
        .filter-alive span{
            margin: auto;
            color: #347AF7;
        }

        .filter-alive-select{
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin: 5px;
        }
        .filter-alive-select .button{
            margin: 0;
        }

        .filter-closed{
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr 3fr;
            border: 1px solid #347AF7;
            border-radius: 2px;
        }
        .filter-closed span{
            margin: auto;
            color: #347AF7;
        }


        .filter-closed-select{
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            margin: 5px;
        }
        .filter-closed-select .button{
            margin: 0;
        }
        .filter-create{
            display: grid;
            grid-template-columns: 1fr;
            margin: 20px 5px;
        }

    </style>
@endsection

@section('after-scripts-end')
<script>
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
</script>
@endsection

@section('content')

    <header class="header-sort">
        <span>Lọc dữ liệu / Tạo bộ lọc</span>
    </header>

    <div class="main">


        <div class="filter-inf">
            <span>Bộ lọc:</span>
            <div class="inf-fi">
                <span><i class="fas fa-calendar-alt"></i>Ngày tạo : 01/08/2018 - 11/08/2018</span>
                <div class="inf-fi-on">
                    <span>S2 - Đang liên hệ</span>
                    <span><i class="fas fa-exclamation"></i>Todo</span>
                </div>
            </div>


        </div>
        <div class="sort-main">
            <span>Lọc theo thời gian</span>
            <button class="button">Chọn khoảng ngày</button>
        </div>

        <div class="sort-main">
            <span>Lọc theo trạng thái</span>
            <div class="filter-alive">
                <span>Alive</span>
                <div class="filter-alive-select">
                    <button class="button button-active">Todo</button>
                    <button class="button ">Remind</button>
                </div>
            </div>

            <div class="filter-closed">
                <span>Closed</span>
                <div class="filter-closed-select">
                    <button class="button ">Won</button>
                    <button class="button ">Lost</button>
                    <button class="button">Cancel</button>
                </div>
            </div>
        </div>

        <div class="sort-main">
            <span>Lọc theo bước sales trong sales funnel</span>
            <div class="work-need">
                @include('.frontend.includes.list-steps')

            </div>
        </div>

        <div class="filter-create">
            <button class="button">Tạo bộ lọc</button>
        </div>

    </div>
    @include('.frontend.includes.footer')

@endsection