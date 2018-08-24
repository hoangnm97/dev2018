@extends ('backend.layouts.master')

@section ('title', 'Quản lý quyền')

@section('page-header')
    <h1> Quản lý nhóm quyền truy cập </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{{ route('backend.app.dashboard') }}"><i class="fa fa-dashboard"></i> Backend</a></li>
    <li class="active">Dashboard</li>
@endsection

@section('content')


    {!! Form::open(['method' => 'POST', 'class' => 'form-horizontal']) !!}

    <div class="box box-default text-12">
        <div class="box-header with-border">
            <h6 class="box-title text-14" style="font-size: 14px"><strong>{{ ($action == 'create' ? 'Create ' : 'Update ') }} role</strong></h6>
        </div>

        <div class="box-body">
            <div class="form-group">
                <label class="control-label col-sm-2" for="slug">Slug</label>
                <div class="col-sm-6">
                    @if($action == 'create')
                        {!! Form::input('text', 'slug', null, ['class' => 'form-control', 'id' => 'slug', 'placeholder' => 'Slug ']) !!}
                    @elseif($action == 'update')
                        <input class="form-control" type="text" value="{{ $role->slug }}" disabled>
                    @endif


                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Name</label>
                <div class="col-sm-6">
                    {!! Form::input('text', 'name', (isset($role)) ? $role->name : null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Tên role']) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Nhóm quyền</label>
                <div class="col-sm-6">
                    @foreach($permission_groups as $permission_group)

                        <label>
                            <input type="radio" name="permission_group" value="{{ $permission_group->id }}" {{ isset($curent_permission_groups[$permission_group->id]) ? 'checked' : '' }}>
                            {{ $permission_group->name }}
                        </label>
                        <div class="clearfix"></div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">{{ ($action == 'create' ? 'Create ' : 'Update ') }}</button>
                </div>
            </div>
        </div>
    </div>




    {!! Form::close() !!}


@endsection