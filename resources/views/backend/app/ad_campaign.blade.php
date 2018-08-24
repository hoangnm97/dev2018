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
            <h3 class="box-title">Chiến dịch quản cáo</h3>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <tbody><tr>
                    <th>ID</th>
                    <th>Chiến dịch</th>
                    <th>Loại chiến dịch</th>
                    <th>Status</th>
                    <th>Start time</th>
                </tr>

                @foreach($adCampaigns as $adCampaign)
                    <tr>
                        <td>{{$adCampaign->id}}</td>
                        <td>{{ $adCampaign->name }}</td>
                        <td>{{ $adCampaign->kpi_type }}</td>
                        <td>{{ $adCampaign->status }}</td>
                        <td>{{ $adCampaign->start_time }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection