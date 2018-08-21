@extends('prototype.header')

@section('content')
    <div id="list" class="container">
        <div class="list-left">
            <ul id="myTab" class="nav nav-tabs">
                @foreach($types as $type => $type_name)
                    <li @if($type == $current_type) class="active" @endif><a
                                href="{{$category}}?type={{$type}}">{{$type_name}}</a></li>
                @endforeach
            </ul>
            <div id="myTabContent" class="tab-content">
                @foreach($types as $type => $type_name)
                    <div @if($type == $current_type) class="tab-pane fade in active" @else class="tab-pane fade"
                         @endif id="{{ $type }}">
                        @foreach($files[$category][$type] as $list)
                            <div class="prototype">
                                <div class="name"><a href="{{ $list['url'] }}"
                                                     target="_blank">{{ $list['name'] }}</a></div>
                                <div class="time">更新于 {{ $list['update_time'] }}<span class="delete"
                                                                                      onclick="modal(this, '{{ $type }}', '{{ $list['name'] }}')"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <div class="list-right">
            <div class="list-up">
                <div class="hit">产品经理会悄悄通过这个按钮传新原型</div>
                <div class="upload" onclick="upload()">上传{{ $types[$current_type] }}</div>
                <div class="warn"><span class="warn-logo"></span>权限开放~维护靠大家</div>
            </div>
            <div class="list-down">
                <div class="public">公告栏</div>
                <div class="content">
                    <p>平台开放中，欢迎提意见~</p>
                    <p>有任何产品上的建议可以 @徐焱茹…</p>
                    <p>有任何功能上的bug，请 @卢杨~</p>
                </div>
                <div class="img"><img src="{{ asset('/images/prototype/public.png') }}"></div>
            </div>
        </div>
        <!--提示框-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">删除</h4>
                    </div>
                    <div class="modal-body">确认删除？</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-confirm" filename="" type=""
                                onclick="deleteFile(this)">确定
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>

        <!--提示框-->
        <div class="modal fade" id="myUpload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myUploadLabel">上传文件</h4>
                    </div>
                    <form action="/prototype/upload" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="file" name="file[]" multiple="multiple">
                        <input type="hidden" name="category_type" value="{{ $category or '' }}">
                        <input type="hidden" name="type" value="{{ $current_type or '' }}">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-confirm">确定</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal -->
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#list .prototype').hover(function () {
                $(this).find('.delete').css('display', 'inline-block');
            }, function () {
                $(this).find('.delete').css('display', 'none');
            });
        });


        function deleteFile(_this) {
            const data = {
                "type": _this.getAttribute('type'),
                "filename": _this.getAttribute('filename'),
                "category": "{{ $category }}"
            };
            $.ajax({
                type: "DELETE",
                url: "/api/prototype",
                data: data,
                dataType: "json",
                beforeSend: function (XMLHttpRequest) {

                }, success: function (response) {
                    location.reload();
                }, error: function () {
                }
            });
        }

        function modal(_this, type, filename) {
            $('#myModal .modal-body').text('确认删除' + filename + '?');
            $('#myModal .btn-confirm').attr('filename', filename);
            $('#myModal .btn-confirm').attr('type', type);
            $("#myModal").modal('show');
        }

        function upload() {
            $("#myUpload").modal('show');
        }
    </script>
@endsection