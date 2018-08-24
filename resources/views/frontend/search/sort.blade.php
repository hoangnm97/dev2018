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


    </style>
@endsection

@section('after-scripts-end')

@endsection

@section('content')

    <header class="header-sort">
        <span>Sắp xếp</span>
    </header>

    <div class="main">
        <div class="sort-main">
            <i class="fas fa-times"></i>
            <span>Sắp xếp theo tên lead</span>
            <div class="sort-name">
                <button class="button button-active"> Từ A đến Z</button>
                <button class="button"> Từ Z đến A</button>
            </div>

            <span>Sắp xếp theo ngày lead được tạo</span>
            <div class="sort-name">
                <button class="button button-active"> Tăng dần</button>
                <button class="button"> Giảm dần</button>
            </div>

            <span>Thời điểm gần đây nhất tương tác</span>
            <div class="sort-name">
                <button class="button button-active"> Tăng dần</button>
                <button class="button"> Giảm dần</button>

            </div>

            <div class="save-sort">
                <button class="button">ÁP DỤNG</button>
            </div>
        </div>

    </div>
    @include('.frontend.includes.footer')

@endsection