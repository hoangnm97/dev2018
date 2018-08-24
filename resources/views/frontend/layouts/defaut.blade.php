<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CSS Grid is good</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <style>


        * { box-sizing: border-box;margin: 0;padding: 0; }
        body {
            font-family: 'Roboto', sans-serif;
        }
        .app {
            display: grid;
            grid-template-rows: 60px auto 50px;
            grid-template-columns: 1fr;
            height: 100vh;

        }
        .header {
            background: #FFFFFF;
            text-align: center;
            line-height: 60px;
            border-bottom: 1px solid #EFF0F3;
            box-shadow: 0 0 25px 0 rgba(0, 0, 0, 0.04);
            display: grid;
            grid-template-columns: auto 80px;
        }
        .main {
            background: #EFF0F3;
            overflow: auto;
        }
        .footer {
            background: #373741;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            text-align: center;
        }
        .footer i {
            color: #FFFFFF;
            line-height: 50px;
        }

        a{
            text-decoration: none;
        }
        input{
            outline: none;
        }

        .hide{
            display: none!important;
        }

        .show{
            display: block;
        }

        .sidebar{
            display: grid;
            grid-template-rows: auto 50px;
            grid-template-columns: 1fr;
            height: 100vh;
        }

        .menu-sidebar{
            display: grid;
            grid-template-columns: 3fr 1fr;
        }

        .list-sidebar{
            background: #343A3B;
            display: grid;
            grid-template-rows: 60px auto 50px;
            grid-template-columns: 1fr;
            border-bottom: 1px solid #212526;
        }

        .sidebar-hover{
            background: rgba(0, 0, 0, 0.7);
        }
        .header-sidebar{
            background: #6E6E6E;
            display: grid;
            grid-template-columns: 1fr 3fr;
            justify-content: center;
        }

        .header-sidebar span{
            color: #C4C4C4;
            margin: auto 20px;
        }

        .header-sidebar img{
            margin: auto;
        }
        .list-menu{
            display: grid;
            grid-template-rows: repeat(6,50px) auto;
            grid-template-columns: 1fr;

        }
        .list-menu li{
            border-bottom: 1px solid #212526;
            padding-left: 20px;
            margin-top: 17px;
            color: white;
            font-size: 14px;
        }
        .list-menu li i{
            margin-right: 15px;
        }

        li{
            list-style-type: none;
        }
        .footer-siderbar{
            color: #888888;
            margin: auto 20px;
        }
    </style>

    @yield('style')
</head>
<body>
    <div class="app">
        @yield('content')
    </div>

    @include('frontend.includes.sidebar')

    <script
            src="https://code.jquery.com/jquery-1.12.4.js"
            integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
            crossorigin="anonymous">
    </script>
    <script>
        $('.click-menu').on('click',function(){

            if($('.app').hasClass('hide')){
                $('.app').removeClass('hide');
                $('.sidebar').addClass('hide');
            }
            else{
                $('.app').addClass('hide');
                $('.sidebar').removeClass('hide');
            }
        })
    </script>

    @yield('after-scripts-end')

</body>
</html>