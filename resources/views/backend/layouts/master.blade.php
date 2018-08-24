<!doctype html>
<html class="no-js" lang="">
<head>
    <title>App Name - @yield('title')</title>
    <meta name="_token" content="{{ csrf_token() }}" />

    @yield('before-styles-end')
{{--    {!! HTML::style('backend/css/backend.css?v=' . time()) !!}--}}

    <link media="all" type="text/css" rel="stylesheet" href="{{ asset('/backend/css/backend.css') . '?v=' . time() }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">


    {{--{!! HTML::style('backend/plugin/bootstrap-modal/css/bootstrap-modal-bs3patch.css') !!}--}}
    {{--{!! HTML::style('backend/plugin/bootstrap-modal/css/bootstrap-modal.css') !!}--}}
    {{--{!! HTML::style('backend/plugin/switchery/switchery.min.css') !!}--}}
    @yield('after-styles-end')

</head>


<body class="">

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="d-block w-100 text-center">Admin Title</div>
</nav>

<div class="container-fluid margin-top-15">
    <div class="row">
        <div class="col-2">
            @include('backend.includes.backend_desktop.sidebar')
        </div>
        <div class="col-10">
            @yield('content')
        </div>
    </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
</script>


{{--<script src="{{ asset('/plugin/bootstrap/dist/js/bootstrap.js') }}"></script>--}}

@yield('before-scripts-end')

<script src="{{ asset('/backend/js/master.js') }}"></script>
<script src="{{ asset('/plugin/JavaScript-Templates-3.11.0/js/tmpl.js') }}"></script>

@yield('after-scripts-end')


@yield('notify-scripts')
<span class="hide" style="display:none">{{microtime(true) - LARAVEL_START}}</span>
</body>

</html>
