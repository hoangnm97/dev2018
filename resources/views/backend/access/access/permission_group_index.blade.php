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

    <div class="box box-default">

        <div class="box-header with-border">
            <a class="btn btn-xs btn-primary pull-right" href="{{ route('access.permission_group.create') }}">+ Thêm nhóm quyền</a>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Permission Group</th>
                    <th>Permission</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($permissionGroups as $permissionGroup)
                    <tr>
                        <td>{{$permissionGroup->name}}</td>
                        <td>
                            @foreach($permissionGroup->permissions as $permission)
                                <p>{{ $permission->name }}</p>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('access.permission_group.update', ['id' => $permissionGroup->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Chỉnh sửa</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
