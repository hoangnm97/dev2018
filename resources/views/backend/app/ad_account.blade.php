@extends ('backend.layouts.master')

@section ('title', 'Tài khoản quản cáo')

@section('after-scripts-end')
    {{--{!! HTML::script('backend/js/searchindex.js') !!}--}}
@endsection

@section('page-header')
    <h1>
        Tài khoản quản cáo
    </h1>
@endsection

@section('content')
    <div class="form-horizontal">

        <div class="form-group">
            <label class="col-sm-2 control-label">AccountID</label>
            <div class="col-sm-10">
                <label class="control-label">{{ $adAccount['account_id'] }}</label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10">
                <label class="control-label">{{ $adAccount['name'] }}</label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Nhà quản cáo</label>
            <div class="col-sm-10">
                <label class="control-label">{{ $adAccount['end_advertiser_name'] }}</label>
            </div>
        </div>
    </div>
@endsection