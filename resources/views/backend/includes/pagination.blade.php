<div class="paginationDiv">
    <div class="col-sm-12">
        <div class="dataTables_info text-center text-small text-muted margin-top-15">
            Đang hiển thị <strong>{!! ($data->total() > $data->perPage()) ? $data->perPage() : $data->total()  !!}</strong> trên tổng số <strong>{!! $data->total() !!}</strong> bản ghi
        </div>
    </div>
    <div class="col-sm-12">
        <div class="dataTables_paginate paging_simple_numbers text-center">
            {!! $data->appends($appended)->render() !!}
        </div>
    </div>
</div>