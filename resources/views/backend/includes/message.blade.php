@if ($errors->any())

  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <h4>Lỗi: </h4>
    <?php
      $messages = $errors->all();
      ?>
    <ul>

    </ul>
    @foreach($messages as $message)
      <li>{{ $message }}</li>
    @endforeach
  </div>
@elseif (Session::get('flash_success'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    @if(is_array(json_decode(Session::get('flash_success'),true)))
      {!! implode('', Session::get('flash_success')->all(':message<br/>')) !!}
    @else
      {!! Session::get('flash_success') !!}
    @endif
  </div>
@elseif (Session::get('flash_warning'))
  <div class="alert alert-warning alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    @if(is_array(json_decode(Session::get('flash_warning'),true)))
      {!! implode('', Session::get('flash_warning')->all(':message<br/>')) !!}
    @else
      {!! Session::get('flash_warning') !!}
    @endif
  </div>
@elseif (Session::get('flash_info'))
  <div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    @if(is_array(json_decode(Session::get('flash_info'),true)))
      {!! implode('', Session::get('flash_info')->all(':message<br/>')) !!}
    @else
      {!! Session::get('flash_info') !!}
    @endif
  </div>
@elseif (Session::get('flash_danger'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    @if(is_array(json_decode(Session::get('flash_danger'),true)))
      {!! implode('', Session::get('flash_danger')->all(':message<br/>')) !!}
    @else
      {!! Session::get('flash_danger') !!}
    @endif
  </div>
@elseif (Session::get('flash_message'))
  <div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    @if(is_array(json_decode(Session::get('flash_message'),true)))
      {!! implode('', Session::get('flash_message')->all(':message<br/>')) !!}
    @else
      {!! Session::get('flash_message') !!}
    @endif
  </div>
@endif

@if (Session::get('checkPublicCourse'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <strong>Hãy hoàn thiện các thông tin sau trước khi xuất bản khóa học.</strong>
    <ol style="list-style:inside">
      @foreach (Session::get('checkPublicCourse') as $msg)
        <li>{{ $msg }}</li>
      @endforeach
      {{ Session::flush() }}
    </ol>
  </div>
@endif