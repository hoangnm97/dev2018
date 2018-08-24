@extends ('backend.layouts.master')

@section('content')
    <div class="p-3 mb-2 heodiv bg-white border rounded">
        <h4 class="">Thiết lập các trường thông tin</h4>

        <div class="mb-3">
            <button class="btn my-btn-green bg-white" data-toggle="modal" data-target="#addGroupModal">Thêm nhóm thông tin</button>
        </div>

        <div id="listingAttributeGroup">
            <div class="card text-13">
                <div class="card-header">

                    <div class="row">
                        <div class="col-6"><strong>Group attribute name</strong></div>
                        <div class="col-6">
                            <div class="text-right">
                                <button class="btn-unstyle text-13"><span class="my-color-red"><i class="fas fa-trash"></i></span> Xóa</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="max-width: 400px">
                        <ul class="list-group my-group">
                            <li class="list-group-item p-2 text-13">
                                <span>Cras justo odio</span>
                                <ul class="ul-action">
                                    <li class="mr-2"><span class="action-item my-color-blue"><i class="fas fa-edit"></i></span></li>
                                    <li><span class="action-item my-color-red"><i class="fas fa-trash"></i></span></li>
                                </ul>

                            </li>
                            <li class="list-group-item p-2 text-13">Dapibus ac facilisis in
                                <ul class="ul-action">
                                    <li class="mr-2"><span class="action-item my-color-blue"><i class="fas fa-edit"></i></span></li>
                                    <li><span class="action-item my-color-red"><i class="fas fa-trash"></i></span></li>
                                </ul>
                            </li>
                            <li class="list-group-item p-2 text-13">Morbi leo risus
                                <ul class="ul-action">
                                    <li class="mr-2"><span class="action-item my-color-blue"><i class="fas fa-edit"></i></span></li>
                                    <li><span class="action-item my-color-red"><i class="fas fa-trash"></i></span></li>
                                </ul>
                            </li>
                            <li class="list-group-item p-2 text-13">Porta ac consectetur ac
                                <ul class="ul-action">
                                    <li class="mr-2"><span class="action-item my-color-blue"><i class="fas fa-edit"></i></span></li>
                                    <li><span class="action-item my-color-red"><i class="fas fa-trash"></i></span></li>
                                </ul>
                            </li>
                            <li class="list-group-item p-2 text-13">Vestibulum at eros
                                <ul class="ul-action">
                                    <li class="mr-2"><span class="action-item my-color-blue"><i class="fas fa-edit"></i></span></li>
                                    <li><span class="action-item my-color-red"><i class="fas fa-trash"></i></span></li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>



    <script type="text/x-tmpl" id="tmpl-attribute-group">
        <div class="card text-13 mt-3">
            <div class="card-header">

                <div class="row">
                    <div class="col-6"><strong>Group attribute name</strong></div>
                    <div class="col-6">
                        <div class="text-right">
                        <button class="btn-unstyle text-13"><span class="my-color-red"><i class="fas fa-trash"></i></span> Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div style="max-width: 400px">
                    <ul class="list-group my-group">

                    </ul>
                </div>

            </div>
        </div>
    </script>

    <script type="text/x-tmpl" id="tmpl-demo">
    <h3>{%=o.title%}</h3>
        <p>Released under the
            <a href="{%=o.license.url%}">{%=o.license.name%}</a>.</p>
        <h4>Features</h4>
        <ul>
            {% for (var i=0; i<o.features.length; i++) { %}
            <li>{%=o.features[i]%}</li>
            {% } %}
        </ul>
    </script>


    <div id="result"></div>
@endsection

@section('after-scripts-end');
    <script type="text/javascript">

        var obj = {
            title: "John",
            license: {
                name: 'THoại văn',
                url: 'https://opensource.org/licenses/MIT'
            },
            features: ['sadfsdgds', 'adafad']
        };



        $(function () {
            document.getElementById("result").innerHTML = tmpl("tmpl-demo", obj);
            console.log('gdsgsdgsfg');
        });

        function addAttributeGroup() {
            $('#listingAttributeGroup').append(tmpl('tmpl-attribute-group', obj));
        }


    </script>
@endsection

