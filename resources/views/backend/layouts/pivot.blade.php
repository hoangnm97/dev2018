<!doctype html>
<html class="no-js" lang="">
<head>
    <title>App Name - @yield('title')</title>

    @yield('before-styles-end')

    <link media="all" type="text/css" rel="stylesheet" href="{{ asset('/plugin/bootstrap/dist/css/bootstrap.min.css') }}">

    <link media="all" type="text/css" rel="stylesheet" href="{{ asset('/backend/css/backend.css') }}">

    @yield('after-styles-end')
</head>


<body>
@yield('content')

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="{{asset('js/vendor/jquery-1.11.2.min.js')}}"><\/script>')</script>
<script>
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
</script>


@include('includes.partials.params')


@yield('before-scripts-end')



@yield('after-scripts-end')

</body>

</html>
