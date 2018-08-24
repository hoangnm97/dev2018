<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12/8/2017
 * Time: 2:58 PM
 */
?>
<div class="login-modal modal fade" id="heoRequiredLoginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="padding: 10px">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="myModalLabel" style="font-size: 15px;font-weight: bold;">Đăng nhập để tiếp tục</h4>
            </div>
            <div class="modal-body" style="padding-top: 0">
                <div class="">
                    <div class="panel-body">
                        <div class="col-md-8 col-md-offset-2">
                            {{--<div class="login_logo">--}}
                                {{--<p class="hide"><a href="{{ url('/') }}" class="logo logo_small"></a></p>--}}
                                {{--<p class="title">Đăng nhập vào {!! config('app.name') !!}</p>--}}
                            {{--</div>--}}
                            <div class="edus-login">
                                <div class="form-group">
                                    <a
                                            href="{{ route('auth.provider',['provider' => 'facebook']) }}"
                                            class="form-control loginwidthfacebook"><i class="fa fa-facebook"></i><span>Đăng nhập bằng facebook</span></a>
                                </div>

                                <div class="form-group">
                                    <a href="{{ route('auth.provider',['provider' => 'google']) }}" class="form-control loginwidthgoogle" ><i class="fa fa-google"></i><span>Đăng nhập bằng google</span></a>
                                </div>

                                @if (getenv('IDVG_CLIENT_ID') != "")
                                    <div class="form-group">
                                        <a href="{{ route('idvg.login',['uri'=>base64_encode(Request::url())]) }}" class="form-control loginwidthvgid" >
                                            <i class="fa fa-vgid"></i>
                                            <span>Đăng nhập bằng <strong>ID</strong> <span class="vg">vatgia</span></span>
                                        </a>
                                    </div>
                                @endif

                            </div>

                            {!! Form::open(['url' => 'auth/login', 'class' => 'edus-login', 'role' => 'form']) !!}
                            <div class="form-group horizon">
                                <h3>
                                    <span>Hoặc điền thông tin đăng nhập bên dưới</span> <hr>
                                </h3>
                            </div>
                            <div class="form-group al_left">
                                {!! Form::input('email', 'email', old('email'), ['class' => 'form-control input_login','placeholder' => 'Email của bạn']) !!}
                                {!! $errors->first('email', '<span class="label label-danger">:message</span>') !!}
                            </div>

                            <div class="form-group al_left">
                                {!! Form::input('password', 'password', null, ['class' => 'form-control input_login','placeholder' => 'Mật khẩu']) !!}
                                {!! $errors->first('password', '<span class="label label-danger">:message</span>') !!}
                            </div>
                            <div class="checkbox al_left">
                                <label>
                                    {!! Form::checkbox('remember') !!} Nhớ mật khẩu
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary send_login">Đăng nhập</button>

                            <div class="form-group foot_login">
                                    <div  class="text-center mg-top-10"><a href="{{ url('password/email') }}" class="">Quên mật khẩu</a></div>
                                <div class="row">
                                </div>
                            </div>

                            <a type="submit" class="btn btn-primary send_register" href="{{ url('auth/register') }}">Đăng ký tài khoản</a>

                            {!! Form::close() !!}
                        </div>
                    </div><!-- panel body -->

                </div><!-- panel -->


            </div>
            {{--<div class="modal-footer">--}}
                {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
                {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
            {{--</div>--}}
        </div>
    </div>
</div>