@if ($breadcrumbs)
  <ol class="breadcrumb">
    @foreach ($breadcrumbs as $breadcrumb)
      @if ($breadcrumb->url && !$breadcrumb->last)
        <li><a href="{{ $breadcrumb->url }}">
            @if(isset($breadcrumb->html_title))
              {!! $breadcrumb->title !!}
              @else
              {{ $breadcrumb->title }}
              @endif
          </a></li>
      @else
        <li class="active">{{ $breadcrumb->title }}</li>
      @endif
    @endforeach
  </ol>
@endif
