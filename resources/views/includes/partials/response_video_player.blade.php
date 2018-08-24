@if($video_player == 'mediaelement')
<div class="embed-responsive embed-responsive-16by9">
  <div class="embed-responsive-item">
    <video id="{{isset($player_id) ? $player_id : ""}}" class="" controls preload="none"
           poster="{{$cover_img}}" {{$autoplay ? "autoplay" : ""}}
           data-setup='{"techOrder": ["flash", "html5"]}'>
      <source src="{{ $stream_link }}" type='video/mp4' />
      {{--<source src="http://video-js.zencoder.com/oceans-clip.webm" type='video/webm' />--}}
      {{--<source src="http://video-js.zencoder.com/oceans-clip.ogv" type='video/ogg' />--}}
      {{--<track kind="captions" src="../shared/example-captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->--}}
      {{--<track kind="subtitles" src="../shared/example-captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->--}}
      <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
    </video>
  </div>
</div>
@elseif($video_player == 'videojs')
<div class="embed-responsive embed-responsive-16by9">
  {{--<div class="embed-responsive-item">--}}
    <video id="{{isset($player_id) ? $player_id : ""}}" class="video-js vjs-default-skin embed-responsive-item" controls preload="auto"
           poster="{{$cover_img}}"  {{$autoplay ? "autoplay" : ""}}
           data-setup='{}'>
      @if(is_array($stream_link))
        @foreach($stream_link as $source)
          <source src="{{array_get($source, 'src', '')}}"
                  type="{{array_get($source, 'type', '')}}"
                  res="{{array_get($source, 'res')}}"
                  label="{{array_get($source, 'label')}}"
                  title="{{array_get($source, 'title')}}" />
        @endforeach
      @else
      @endif
      @if(isset($addition))
        @if(is_array($addition) && isset($addition['url']))
            <track kind="{{array_get($addition, 'type', 'captions')}}"
                   src="{{array_get($addition, 'url', '')}}"
                   srclang="{{array_get($addition, 'lang', 'vi')}}"
                   label="{{array_get($addition, 'label', 'Tiếng Việt')}}"
               default>
        @else
            @foreach($addition as $add)
              <track kind="{{array_get($add, 'type', 'subtitle')}}"
                     src="{{array_get($add, 'url', '')}}"
                     srclang="{{array_get($add, 'lang', 'VN')}}"
                     label="{{array_get($add, 'label', 'Tiếng Việt')}}"
                {{array_get($add, 'default', '')}}>
            @endforeach
        @endif
      @endif

      {{--<source src="http://video-js.zencoder.com/oceans-clip.webm" type='video/webm' />--}}
      {{--<source src="http://video-js.zencoder.com/oceans-clip.ogv" type='video/ogg' />--}}
      {{--<track kind="captions" src="../shared/example-captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->--}}
      {{--<track kind="subtitles" src="../shared/example-captions.vtt" srclang="en" label="English"></track><!-- Tracks need an ending tag thanks to IE9 -->--}}
      <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
    </video>
    <?php if(isset($player_id))add_multi_source_player($player_id); ?>
  {{--</div>--}}
</div>
@elseif($video_player == 'clappr')
<div class="embed-responsive embed-responsive-16by9">
  <?php
  $_players = get_multi_source_players();
  $player_id = isset($player_id) ? $player_id : "clappr_" . count($_players);
  $_player = [
    'id' => $player_id,
    'cover' => $cover_img,
    'stream' => $stream_link,
    'addition' => $addition,
    'autoplay' => $autoplay
  ];
  add_multi_source_player($_player);
  ?>
  <div id="{{$player_id}}" class="embed-responsive-item">
    <img class="embed-responsive-item" src="{{$cover_img}}" class="img-responsive" />
  </div>
  {{--</div>--}}
</div>
@endif