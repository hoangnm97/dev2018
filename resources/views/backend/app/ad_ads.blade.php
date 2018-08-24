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
            <h3 class="box-title">Quản cáo</h3>
        </div>

        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <tbody><tr>
                    <th>ID</th>
                    <th>Tên QC</th>
                    <th>Status</th>
                    <th>Created time</th>
                </tr>

                @foreach($ads as $ad)
                    <tr>
                        <td>{{ $ad->id }}</td>
                        <td>{{ $ad->name }}</td>
                        <td>{{ $ad->status }}</td>
                        <td>{{ $ad->created_time }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection