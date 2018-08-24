
@extends('.frontend.layouts.defaut')

@section('style')
<style>
    .card-login {
        display: grid;
        grid-template-columns: 1fr;
        grid-gap: 5px;
        margin: 10px;
        padding: 10px;
        background: #FFFFFF;
        box-shadow: 0px 1px 8px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }
    .logo {
        text-align: center;
    }
    .input-label {
        color: #888888;
    }
    .standard-input {
        width: 100%;
        border: 1px solid #DADADA;
        height: 40px;
        border-radius: 5px;
    }
    .input-group {
        margin-bottom: 20px;
    }
    .primary-button {
        height: 40px;
        background: #FFFFFF;
        border: 1px solid #347AF7;
        text-transform: uppercase;
        border-radius: 5px;
        color: #347AF7;
        cursor: pointer;
    }
    .input-label {
        padding-left: 7px;
        margin-bottom: 5px;
    }
    .standard-input {
        padding-left: 7px;
        color: #888888;
        font-size: 1em;
    }
    input::placeholder {
        color: #DADADA;
        /*padding-left: 7px;*/
    }

    button{
        outline: none;
    }
    input{
        outline: none;
    }
</style>
@endsection

@section('after-scripts-end')

@endsection

@section('content')
    <header class="header">Sigin</header>
    <div class="main">
        <div class="card-login">
            <div class="logo">Logo</div>
            <div class="input-group">
                <div class="input-label">Client PIN</div>
                <input type="password" class="standard-input" placeholder="PIN">
            </div>
            <div class="input-group">
                <div class="input-label">Username</div>
                <input type="text" class="standard-input" placeholder="Tên đăng nhập">
            </div>
            <div class="input-group">
                <div class="input-label">Password</div>
                <input type="password" class="standard-input" placeholder="Mật khẩu">
            </div>
            <button class="primary-button"><a href="index">Sign in</a></button>
        </div>
    </div>
    @include('.frontend.includes.footer')
@endsection