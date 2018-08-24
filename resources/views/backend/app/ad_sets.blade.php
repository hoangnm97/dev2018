@extends ('backend.layouts.master')

@section ('title', 'Tài khoản quản cáo')

@section('after-scripts-end')
    {{--{!! HTML::script('backend/js/searchindex.js') !!}--}}
@endsection

@section('page-header')

@endsection

@section('content')
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Nhóm quản cáo</h3>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <tbody><tr>
                    <th>ID</th>
                    <th>Tên nhóm</th>
                    <th>Daily Budget</th>
                    <th>Lifetime Budget</th>
                    <th>Status</th>
                    <th>Start time</th>
                </tr>

                @foreach($adSets as $adSet)
                    <tr>
                        <td>{{ $adSet->id }}</td>
                        <td>{{ $adSet->name }}</td>
                        <td>{{ human_money($adSet->daily_budget, '0đ') }}</td>
                        <td>{{ human_money($adSet->lifetime_budget, '0đ') }}</td>
                        <td>{{ $adSet->status }}</td>
                        <td>{{ $adSet->start_time }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection