          <!-- Main Footer -->
          <footer class="main-footer">
            <!-- To the right -->
            <div class="pull-right hidden-xs">
              {{trans('common.footer_right', ['name' => config('app.url')])}}
            </div>
            <!-- Default to the left -->

            <strong>{!! trans('common.footer_left',['year' => 2016, 'link' => config('app.url'), 'name' => config('app.name')]) !!}</strong>
          </footer>