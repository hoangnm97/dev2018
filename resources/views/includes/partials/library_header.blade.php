<div class="panel unibee-box">
  <div class="panel-heading">
    <h3 class="panel-title">Quản lý thư viện</h3>
  </div>
  <?php if(!isset($media_type)) $media_type = ''; ?>
  <div class="panel-body no-padding" style="padding-bottom: 10px;">
    <a href="{{route('teacher.my_library', ['media_type' => 'video'])}}"
       class="btn btn-primary {{$media_type == 'video' ? "active" : ""}}">Video</a>
    <a href="{{route('teacher.my_library', ['media_type' => 'document'])}}"
       class="btn btn-primary {{$media_type == 'document' ? "active" : ""}}">Document</a>
    <a href="{{route('teacher.my_library', ['media_type' => 'audio'])}}"
       class="btn btn-primary {{$media_type == 'audio' ? "active" : ""}}">Audio</a>

      <span class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="true">
          <i class="fa fa-plus"></i> Thêm mới
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
          <li><a href="{{route('teacher.my_library.add_video')}}">Thêm Video</a></li>
          <li><a href="{{route('teacher.my_library.add_document')}}">Thêm Document</a></li>
          <li><a href="{{route('teacher.my_library.add_audio')}}">Thêm Audio</a></li>
        </ul>
      </span>
  </div>
</div>