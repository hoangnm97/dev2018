@extends ('backend.layouts.master')

@section ('title', 'Quản lý quyền')



@section('page-header')
    <h1> Quản lý vai trò </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{{ route('backend.app.dashboard') }}"><i class="fa fa-dashboard"></i> Backend</a></li>
    <li class="active">Dashboard</li>
@endsection

@section('content')

    <div class="box box-default">
        <div class="box-header with-border">
            <a class="btn btn-xs btn-primary pull-right" href="{{ route('access.role.create') }}">+ Thêm role</a>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Role</th>
                    <th>Permission Group</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{$role->name}}</td>
                        <td>
                            @foreach($role->permissionGroups as $permissionGroup)
                                {{ $permissionGroup->name }}
                            @endforeach

                        </td>
                        <td class="">
                            <a href="{{ route('access.role.update', ['id' => $role->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Chỉnh sửa</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
