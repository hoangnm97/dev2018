@if ($errors->any())

  @section('notify-scripts')
    <script>
        $.notify({
          message: 'Có lỗi xảy ra, vui lòng kiểm tra lại!'
        },{
          type: 'danger'
        });
    </script>
  @endsection

@elseif (Session::get('flash_success'))

  @if(is_array(json_decode(Session::get('flash_success'),true)))
    
    @section('notify-scripts')
      <script>
          $.notify({
            message: '{!! addslashes(implode('', Session::get('flash_success')->all(':message<br/>'))) !!}'
          },{
            type: 'success'
          });
      </script>
    @endsection

  @else

    @section('notify-scripts')
      <script>
          $.notify({
            message: '{!! addslashes(Session::get('flash_success')) !!}'
          },{
            type: 'success'
          });
      </script>
    @endsection

  @endif

@elseif (Session::get('flash_warning'))

  @if(is_array(json_decode(Session::get('flash_warning'),true)))
      
      @section('notify-scripts')
        <script>
            $.notify({
              message: '{!! addslashes(implode('', Session::get('flash_warning')->all(':message<br/>'))) !!}'
            },{
              type: 'warning'
            });
        </script>
      @endsection

  @else
    
    @section('notify-scripts')
      <script>
          $.notify({
            message: '{!! addslashes(Session::get('flash_warning')) !!}'
          },{
            type: 'warning'
          });
      </script>
    @endsection

  @endif

@elseif (Session::get('flash_info'))

  @if(is_array(json_decode(Session::get('flash_info'),true)))
    
    @section('notify-scripts')
      <script>
          $.notify({
            message: '{!! addslashes(implode('', Session::get('flash_info')->all(':message<br/>'))) !!}'
          },{
            type: 'info'
          });
      </script>
    @endsection

  @else
    
    @section('notify-scripts')
      <script>
          $.notify({
            message: '{!! addslashes(Session::get('flash_info')) !!}'
          },{
            type: 'info'
          });
      </script>
    @endsection

  @endif

@elseif (Session::get('flash_danger'))
  
  @if(is_array(json_decode(Session::get('flash_danger'),true)))
    
    @section('notify-scripts')
      <script>
          $.notify({
            message: '{!! addslashes(implode('', Session::get('flash_danger')->all(':message<br/>'))) !!}'
          },{
            type: 'danger'
          });
      </script>
    @endsection

  @else
    
    @section('notify-scripts')
      <script>
          $.notify({
            message: '{!! addslashes(Session::get('flash_danger')) !!}'
          },{
            type: 'danger'
          });
      </script>
    @endsection

  @endif

@elseif (Session::get('flash_message'))

  @if(is_array(json_decode(Session::get('flash_message'),true)))
    
    @section('notify-scripts')
      <script>
          $.notify({
            message: '{!! addslashes(implode('', Session::get('flash_message')->all(':message<br/>'))) !!}'
          },{
            type: 'info'
          });
      </script>
    @endsection

  @else
    
    @section('notify-scripts')
      <script>
          $.notify({
            message: '{!! addslashes(Session::get('flash_message')) !!}'
          },{
            type: 'info'
          });
      </script>
    @endsection

  @endif

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