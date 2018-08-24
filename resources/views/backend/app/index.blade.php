@extends ('backend.layouts.master')

@section ('title', 'HEO')

@section('after-scripts-end')
    {{--{!! HTML::script('backend/js/searchindex.js') !!}--}}
@endsection

@section('page-header')
    <h1> Dashboard </h1>
@endsection

@section('breadcrumbs')
    <li><a href="{{ route('backend.app.dashboard') }}"><i class="fa fa-dashboard"></i> Backend</a></li>
    <li class="active">Dashboard</li>
@endsection

@section('content')
    <h5>Xin chào <strong>{{ Auth::user()->name }}</strong>!</h5>
@endsection