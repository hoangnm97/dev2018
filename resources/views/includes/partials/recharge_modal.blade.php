<div class="heo-payment-wrap  modal unibee-model fade" id="recharge_popup_form" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="close triangle-topright" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </div>
        <h4 class="modal-title" id="myModalLabel">Nạp tiền mua khóa học</h4>
      </div>
      {!! Form::open(['name' => 'recharge_form', 'id' => 'recharge_form', 'class' => 'form-group-sm']) !!}
      <div class="panel panel-recharge">
        <div class="panel-body">
          <div class="row">
            <div class="account-box clearfix">
              <div class="col-md-4">
                <div class="tiny-profile clearfix">
                  {{--<img src="{{auth()->user()->showAvatar()}}" alt="{{auth()->user()->name}}"--}}
                       {{--class="avatar img-responsive img-circle">--}}

                  <p class="info">
                    <a class="name" title="{{auth()->user()->name}}" href="#">{{auth()->user()->name}}</a>
                  </p><p class="info">
                    <span class="email">{{auth()->user()->email}}</span>
                  </p>
                </div>
              </div>
              <div class="col-md-4">
                <h6>Tài khoản: {{auth()->user()->secondaryAmount('view')}}<span class="small">đ</span></h6>
              </div>
              <div class="col-md-4">
                <h6>Doanh thu: {{auth()->user()->primaryAmount('view')}}<span class="small">đ</span></h6>
              </div>
            </div>
          </div>
          <div class="recharge_methods_tabs">
            <ul class="nav nav-payment-methods" role="tablist">
              <li role="presentation" class="">
                <a href="#recharge_by_mobile_card" class="by-mobile-card" id="recharge_by_mobile_card_control"
                   aria-controls="recharge_by_mobile_card" role="tab" data-toggle="tab">
                  Thẻ điện thoại
                </a>
              </li>
              <li role="presentation" class="">
                <a href="#recharge_by_bank_card" class="by-bank-card" id="recharge_by_bank_card_control"
                   aria-controls="recharge_by_bank_card" role="tab" data-toggle="tab">
                  Thẻ ngân hàng
                </a>
              </li>
              <li role="presentation" class="">
                <a href="#recharge_by_bank_exchange" class="by-bank-exchange" id="recharge_by_bank_exchange_control"
                   aria-controls="recharge_by_bank_exchange" role="tab" data-toggle="tab">
                  Chuyển khoản
                </a>
              </li>
            </ul>
            <!-- Hidden fields -->
            <input type="hidden" name="recharge_by" id="recharge_by" value="mobile_card"/>
            <!-- Tab panes -->
            <div class="row">
              <div class="tab-content no-border col-sm-6">
                <div role="tabpanel" class="tab-pane active" id="recharge_by_mobile_card"><i
                    class="fa fa-spinner fa-spin"></i></div>
                <div role="tabpanel" class="tab-pane" id="recharge_by_bank_card"><i class="fa fa-spinner fa-spin"></i>
                </div>
                <div role="tabpanel" class="tab-pane" id="recharge_by_bank_exchange"><i
                    class="fa fa-spinner fa-spin"></i></div>
                <div class="form-group">
                  {!! app('captcha')->display() !!}
                </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-primary btn-block">
                    Nạp tiền
                  </button>
                </div>
              </div>
              <div class="col-sm-6 text-center recharge_notice" style="">
                <img class="toggle recharge_by_mobile_notice" src="/frontend/img/common/mobile_card_intro.png" style="display: none" />
              </div>
            </div>
          </div>

          <div class="recharge_notice">
            <div class="toggle recharge_by_mobile_notice" style="display: none">
              <ul>
                <li>
                  <?php $mbc_discount = config('money.' . config("app.id") . '.card_account.default.discount'); ?>
                  Phí nạp thẻ {{$mbc_discount}}% cho nhà mạng (VD: nạp thẻ 100.000đ sẽ được cộng {{human_money((100-$mbc_discount)*1000)}} vào tài khoản)
                </li>
                <li>
                  Nạp sai 5 lần liên tiếp, tài khoản của bạn không thể sử dụng hình thức này trong 24h tiếp theo
                </li>
                <li>
                  Nếu gặp lỗi vui lòng xem hướng dẫn tại đây Mọi thắc mắc vui lòng liên hệ: {!! config('common.'.config("app.id").'.contact.telephone') !!}
                </li>
              </ul>
            </div>
            <div class="toggle recharge_by_bank_card_notice" style="display: none">
              <ul>
                <li>
                  Sau khi nạp tiền, hệ thống tự động chuyển sang Smartlink (Ngân hàng) để tiếp tục.
                </li>
                <li>
                  Phí giao dịch ngân hàng thu: Thẻ ATM: 1.760 VND + 1.1% Thẻ Visa/Master/JBC: 5.000VND + 2.4%.
                </li>
                <li>
                  Nếu gặp lỗi vui lòng xem hướng dẫn tại đây Mọi thắc mắc vui lòng liên hệ: {!! config('common.'.config("app.id").'.contact.telephone') !!}
                </li>
                <li class="note-pay">
                  <strong>Lưu ý : </strong>
                  <em>
                  việc thanh toán bằng các loại thẻ quốc tế có thể sẽ cần kiểm duyệt nên chưa được cộng tiền ngay. Thẻ ATM có thể lỗi do các ngân hàng kết nối. Nếu thanh toán bị lỗi bạn nên chuyển sang hình thức nạp thẻ cào hoặc chuyển khoản.
                  </em>
                </li>
              </ul>
            </div>
            <div class="toggle recharge_by_bank_exchange_notice" style="display: none">
              <ul>
                <li>
                  Chuyển khoản vào ngân hàng đã chọn theo hướng dẫn.
                </li>
                <li>
                  Mọi thắc mắc vui lòng liên hệ: {!! config('common.'.config("app.id").'.contact.telephone') !!}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      {!! Form::hidden('back_link', \Request::url()) !!}
      {!! Form::close() !!}
    </div>
  </div>
</div>