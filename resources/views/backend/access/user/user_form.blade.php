@extends ('backend.layouts.master')

@section ('title', 'User form')



@section('page-header')
    <h1> Quản lý User </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{{ route('backend.app.dashboard') }}"><i class="fa fa-dashboard"></i> Backend</a></li>
    <li class="active">Dashboard</li>
@endsection

@section('content')
    <div class="box box-default text-12">
        <div class="box-header with-border">
            <h6 class="box-title text-14" style="font-size: 14px"><strong>{{ ($action == 'update') ? 'Update ' : 'Create ' }} user</strong></h6>
        </div>
        <div class="box-body">
            {!! Form::open([
                'name' => 'createUser',
                'method' => 'POST',
                'class' => 'form-horizontal',
                'autocomplete' => 'off'
            ]) !!}

            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Name</label>
                <div class="col-sm-6">
                    {!! Form::input('text', 'name', ($action == 'update') ? $user->name : null, ['class' => 'form-control', 'placeholder' => 'Enter name']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Email</label>
                <div class="col-sm-6">
                    {!! Form::input('email', 'email', ($action == 'update') ? $user->email : null, ['class' => 'form-control', 'placeholder' => 'Enter email', 'autocomplete' => 'off']) !!}
                </div>
            </div>


            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Password</label>
                <div class="col-sm-6">
                    {!! Form::input('password', 'password', null, ['class' => 'form-control', 'placeholder' => 'Enter password', 'autocomplete' => 'new-password']) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Team</label>
                <div class="col-sm-6">
                    {!! Form::select('team', \App\User::teamList(), null, ['class' => 'form-control'], []) !!}
                </div>
            </div>


            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Roles</label>
                <div class="col-sm-6">
                    @foreach($roles as $role)
                        <div class="">
                            <label>
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ isset($curent_roles[$role->id]) ? 'checked' : '' }}>
                                {{ $role->name }}
                            </label>
                        </div>
                    @endforeach

                </div>
            </div><div class="form-group">
                <label class="control-label col-sm-2" for="name"></label>
                <div class="col-sm-6">
                    {!! Form::submit(($action == 'update') ? 'Update ' : 'Create ', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>

@endsection