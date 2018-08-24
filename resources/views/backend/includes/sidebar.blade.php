
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
      <div class="pull-left image">
        @if (Auth::guest())
          <img src="" alt="" class="mini-pic">
        @else
          <img src="" alt="" class="mini-pic">
        @endif
      </div>
      <div class="info">
        <!-- Status -->
        <a href="#"><i class="fa fa-circle text-success"></i> {{ Auth::user()->name }}</a>
      </div>
    </div>

    <ul class="sidebar-menu tree text-12" data-widget="tree">

        @if(Auth::user()->canDo('system.user.manager'))
            <li class="header">Quản lý User</li>

            <li>
                <a href="{{ route('access.user.manager') }}">
                    <i class="fa fa-th"></i> <span>Danh sách user</span>
                </a>
            </li>

            <li class="header">Roles and Permissions</li>

            <li>
                <a href="{{ route('access.role.manager') }}">
                    <i class="fa fa-th"></i> <span>Roles</span>
                </a>
            </li>

            <li>
                <a href="{{ route('access.permission_group.manager') }}">
                    <i class="fa fa-th"></i> <span>Permission Groups</span>
                </a>
            </li>

            <li>
                <a href="{{ route('access.permission.manager') }}">
                    <i class="fa fa-th"></i> <span>Permissions</span>
                </a>
            </li>

        @endif

        @if(Auth::user()->canDo('marketing.ad_account.manager'))
          <li class="header">Tài khoản quảng cáo</li>
          <li>
            <a href="{{ route('backend.ad_account.index') }}">
              <i class="fa fa-th"></i> <span>Danh sách</span>
            </a>
          </li>
        @endif



        @if(Auth::user()->canDo('lead.view'))

              <li class="header">Leads</li>
              <li>
                <a href="{{ route('backend.lead.index') }}">
                  <i class="fa fa-th"></i> <span>Danh sách Leads</span>
                </a>
              </li>
        @endif







        @if(Auth::user()->canDo('marketing.report.view'))

          <li class="header">Marketing</li>
          <li>
            <a href="{{ route('backend.marketing.report_general') }}">
              <i class="fa fa-th"></i> <span>Báo cáo tổng</span>
            </a>
          </li>
          {{--<li>--}}
            {{--<a href="{{ route('backend.report.fb_general') }}">--}}
              {{--<i class="fa fa-th"></i> <span>Báo cáo FA tổng</span>--}}
            {{--</a>--}}
          {{--</li>--}}
          <li>
            <a href="{{ route('backend.report.fb_optimal') }}">
              <i class="fa fa-th"></i> <span>Báo cáo tối ưu</span>
            </a>
          </li>

          {{--<li>--}}
            {{--<a href="{{ route('backend.marketing.l9_pivot') }}" target="_blank">--}}
              {{--<i class="fa fa-th"></i> <span>L9 Pivot</span>--}}
            {{--</a>--}}
          {{--</li>--}}

          <li>
            <a href="{{ route('backend.marketing.import_external') }}">
              <i class="fa fa-th"></i> <span>Import báo cáo</span>
            </a>
          </li>

          {{--<li>--}}
            {{--<a href="{{ route('backend.report.fb_optimal') }}">--}}
              {{--<i class="fa fa-th"></i> <span>Báo cáo tối ưu</span>--}}
            {{--</a>--}}
          {{--</li>--}}
        @endif


        @if(Auth::user()->canDo('sale.report.view'))

              <li class="header">Sale</li>
              <li>
                <a href="{{ route('backend.sale.working') }}">
                  <i class="fa fa-th"></i> <span>Màn hình làm việc</span>
                </a>
              </li>
                <li>
                    <a href="{{ route('backend.sale.working_re_sale') }}">
                        <i class="fa fa-th"></i> <span>Kho học thử</span>
                    </a>
                </li>
              {{--<li>--}}
                {{--<a href="{{ route('backend.report.general') }}">--}}
                  {{--<i class="fa fa-th"></i> <span>Báo cáo theo ngày</span>--}}
                {{--</a>--}}
              {{--</li>--}}

              <li>
                <a href="{{ route('backend.report.report_ba') }}">
                  <i class="fa fa-th"></i> <span>Báo cáo tỷ lệ chốt</span>
                </a>
              </li>

            @if(Auth::user()->canDo('report.sale.assign_s0'))
                <li>
                    <a href="{{ route('backend.report.assign_s0') }}">
                        <i class="fa fa-th"></i> <span>Báo cáo phân lead</span>
                    </a>
                </li>
            @endif
        @endif


        <li class="header">Báo cáo chung</li>

        @if(Auth::user()->canDo('report.s12.revenue_cutoff'))
            <li>
                <a href="{{ route('backend.report.report_s12_cutoff') }}">
                    <i class="fa fa-th"></i> <span>Báo cáo doanh thu</span>
                </a>
            </li>
        @endif

        @if(Auth::user()->canDo('report.s12.created_time'))
            <li>
                <a href="{{ route('backend.report.report_s12_created_time') }}">
                    <i class="fa fa-th"></i> <span>Báo cáo S12 created time</span>
                </a>
            </li>
        @endif

        @if(Auth::user()->canDo('report.s12.transport'))
            <li>
                <a href="{{ route('backend.report.report_s12_transport') }}">
                    <i class="fa fa-th"></i> <span>Báo cáo giao vận</span>
                </a>
            </li>
        @endif

        @if(Auth::user()->canDo('report.interaction.created'))
            <li>
                <a href="{{ route('backend.report.interaction_report') }}">
                    <i class="fa fa-th"></i> <span>Báo cáo trạng thái S</span>
                </a>
            </li>
        @endif
        @if(Auth::user()->canDo('report.lead.follow'))
            <li>
                <a href="{{ route('backend.report.lead_flow_report') }}">
                    <i class="fa fa-th"></i> <span>Báo cáo gói Lead theo ngày</span>
                </a>
            </li>
        @endif


      {{--<li>--}}
        {{--<a href="{{ route('backend.report.sale_t10') }}">--}}
          {{--<i class="fa fa-th"></i> <span>Báo cáo T+10</span>--}}
        {{--</a>--}}
      {{--</li>--}}

    </ul>

    <!-- search form (Optional) -->
    {{--<form action="#" method="get" class="sidebar-form">--}}
      {{--<div class="input-group">--}}
        {{--<input type="text" name="q" class="form-control" placeholder="Search..."/>--}}
        {{--<span class="input-group-btn">--}}
          {{--<button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>--}}
        {{--</span>--}}
      {{--</div>--}}
    {{--</form>--}}
    <!-- /.search form -->

    <!-- Sidebar Menu -->

    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>
