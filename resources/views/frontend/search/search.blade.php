@extends('.frontend.layouts.defaut')


@section('style')
    <style>
        .header-search{
            background: #FFFFFF;
            line-height: 60px;
            border-bottom: 1px solid #EFF0F3;
            box-shadow: 0 0 25px 0 rgba(0, 0, 0, 0.04);
            display: grid;
            grid-template-columns: 30px 6fr;
            color: #DADADA;
            font-size: 15px;
        }
        .header-search i{
            margin-top: auto;
            margin-bottom: auto;
            margin-left: 20px;
            font-size: 16px;
        }

        .header-search input{
            font-size: 1em;
            padding: 10px;
        }

        input::placeholder{
            color: #888888;
        }

        .search{
            display: grid;
            grid-template-columns: 40px 6fr;
        }

        .button{
            background: white;
            border: 1px solid #347AF7;
            padding: 5px 0;
            font-size: 15px;
            border-radius: 2px;
            outline: none;
            margin: 0 20px;
            color: #347AF7;
        }

        .option-filter{
            display: grid;
            grid-template-rows: repeat(3,50px) auto;
            text-align: center;
        }

        .option-filter span{
            color: #888888;
            font-size: 14px;
            margin: auto;
        }
        .filter-created ul li{
            margin-bottom: 20px;
            color: #347AF7;
        }


    </style>
@endsection

@section('after-scripts-end')

@endsection

@section('content')

    <header class="header-search">
            <i class="fas fa-angle-left"></i>
        <div class="search">
            <i class="fas fa-search"></i>
            <input placeholder="Nhập thông tin tìm kiếm" />

        </div>
    </header>

    <div class="main">
        <div class="option-filter">
            <span>hoặc</span>
            <button class="button">Mở chức năng lọc</button>
            <span>Bộ lọc tự tạo</span>
            <div class="filter-created">
                <ul>
                    <li>Bộ lọc tự tạo 1</li>
                    <li>Bộ lọc tự tạo 2</li>
                    <li>Bộ lọc tự tạo 3</li>
                </ul>

            </div>
        </div>



    </div>
    @include('.frontend.includes.footer')

@endsection