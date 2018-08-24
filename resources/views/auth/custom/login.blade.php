<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>{{ config('app.name') }} - Login</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('css/sigin.css') }}" rel="stylesheet">
</head>

<body class="text-center">

    <form class="form-signin" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <h1 class="h3 mb-3 font-weight-normal">{{ config('app.name') }} sign in</h1>

        <input type="email" id="inputEmail" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>

        <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p class="mt-5 mb-3 text-muted">© 2017-2018</p>
    </form>


</body></html>