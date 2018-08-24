@extends ('backend.layouts.master')

@section ('title', 'Quản lý quyền')



@section('page-header')
    <h1> Quản lý quyền truy cập </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{{ route('backend.app.dashboard') }}"><i class="fa fa-dashboard"></i> Backend</a></li>
    <li class="active">Dashboard</li>
@endsection

@section('content')

    <div class="box box-default">
        <div class="box-header with-border">
            <a class="btn btn-xs btn-primary pull-right" href="{{ route('access.permission.create') }}">+ Thêm quyền</a>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Permission</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($permissions as $permission)
                    <tr>
                        <td>{{$permission->name}}</td>
                        <td class="">
                            <a href="{{ route('access.permission.update', ['id' => $permission->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Chỉnh sửa</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
