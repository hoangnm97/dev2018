@extends ('backend.layouts.master')

@section ('title', 'Danh sách user')



@section('page-header')
    <h1> Quản lý User </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{{ route('backend.app.dashboard') }}"><i class="fa fa-dashboard"></i> Backend</a></li>
    <li class="active">Dashboard</li>
@endsection

@section('content')

        <div class="box-header with-border">
            Danh sách User
            <a class="btn btn-primary btn-xs pull-right" href="{{ route('access.user.create') }}">+ Create new user</a>
        </div>

        <div class="overFlowDiv">
            <div class="pandaTableRender">

                <table class="table table-hover table-striped text-12 panda-table">

                    <thead>
                    <tr class="listViewHeaders">
                        <th style="width: 5%">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Created time</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>

                    {!! Form::open([
                            'route' => 'access.user.manager',
                            'name' => 'SearchTable',
                            'id' => 'searchTable',
                            'class' => 'filter',
                            'role' => 'form',
                            'method' => 'GET',
                            'enctype'=>'multipart/form-data',
                            'autocomplete' => 'off'
                            ])
                         !!}
                    <tr class="">
                        <td>
                            <div class="search-table">
                                {!! Form::input('text', 'id', null, ['class' => 'table-search-input']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="search-table">
                                {!! Form::input('text', 'name', null, ['class' => 'table-search-input']) !!}
                            </div>
                        </td>
                        <td>
                            <div class="search-table">
                                {!! Form::input('text', 'email', null, ['class' => 'table-search-input']) !!}
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                        <td>
                            {!! Form::submit('Search') !!}
                        </td>
                    </tr>
                    {!! Form::close() !!}

                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                <ul class="tag-ul">
                                @foreach($user->roles as $role)
                                    <li class="tag-btn {{ $role->name }}"> <i class="fa fa-user"></i> {{ $role->name }}</li>
                                @endforeach
                                </ul>

                            </td>
                            <td>{{$user->created_at}}</td>
                            <td class="">
                                <a href="{{ route('access.user.update', ['id' => $user->id]) }}" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Chỉnh sửa</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            @include('backend.includes.pagination', [
                    'data' => $users,
                    'appended' => [
                        'id' => Request::get('id'),
                        'name' => Request::get('name'),
                        'email' => Request::get('email')
                        ]
                    ])

        </div>




@endsection
