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
            <h6 class="box-title text-14" style="font-size: 14px"><strong>{{ ($action == 'created' ? 'Create ' : 'Update ') }} permission group</strong></h6>
        </div>

        <div class="box-body">
            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Name</label>
                <div class="col-sm-6">
                    {!! Form::input('text', 'name', (isset($permissionGroup)) ? $permissionGroup->name : null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'Tên nhóm quyền ']) !!}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="name">Permissions</label>
                <div class="col-sm-6">
                    @foreach($permissions as $permission)

                        <label>
                        <input type="checkbox" name="permission[]" value="{{ $permission->id }}" {{ isset($curent_permissions[$permission->id]) ? 'checked' : '' }}>
                            {{ $permission->name }}
                        </label>
                        <div class="clearfix"></div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">{{ ($action == 'created' ? 'Create ' : 'Update ') }}</button>
                </div>
            </div>
        </div>
    </div>




    {!! Form::close() !!}


@endsection