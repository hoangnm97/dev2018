<h4 class="notes-list-header">Ghi chú bài giảng</h4>

<div class="list-group list-group-notes">
  <div class="list-group-item demo-item">
    <div class="note-content"> <i class="fa fa-plus"></i> Thêm ghi chú </div>
    <div class="note-timer">00:00</div>
    <span class="pull-right icom-times-circle remove-note"></span>
  </div>
  @foreach($notes as $note)
  <div class="list-group-item saved-item" data-id="{{$note->id}}">
    <div class="note-content" data-pk="{{$note->id}}"> {{ $note->content }} </div>
    <div class="note-timer"> {{$note->created_at->format('H:i:s d/m/y')}} </div>
    <span class="pull-right icom-times-circle remove-note"></span>
  </div>
  @endforeach
  {{--<div class="list-group-item demo-item">--}}
    {{--<div class="note-content"> <i class="fa fa-plus"></i> Thêm ghi chú </div>--}}
    {{--<div class="note-timer">00:00</div>--}}
    {{--<span class="pull-right glyphicon glyphicon-remove-circle remove-note"></span>--}}
  {{--</div>--}}
</div>
