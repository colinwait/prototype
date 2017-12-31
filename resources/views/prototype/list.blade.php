@extends('prototype.header')

@section('content')
    <div id="list" class="container">
        <div class="list-left">
            <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#prototype" data-toggle="tab">公开原型</a></li>
                <li><a href="#pdf" data-toggle="tab">公开文档</a></li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="prototype">
                    @foreach($lists as $list)
                        <div class="prototype">
                            <div class="name"><a href="{{ $list['url'] }}"
                                                 target="_blank">{{ $list['name'] }}</a></div>
                            <div class="time">更新于 {{ $list['update_time'] }}<span class="delete"
                                                                                  onclick="modal(this, 'prototype', '{{ $list['name'] }}')"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="tab-pane fade" id="pdf">
                    @foreach($pdfs as $pdf)
                        <div class="prototype">
                            <div class="name"><a href="{{ $pdf['url'] }}" target="_blank">{{ $pdf['name'] }}</a></div>
                            <div class="time">更新于 {{ $pdf['update_time'] }}<span class="delete"
                                                                                 onclick="modal(this, 'pdf', '{{ $pdf['name'] }}')"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="list-right">
            <div class="list-up">
                <div class="hit">产品经理会悄悄通过这个按钮传新原型</div>
                <div class="upload" onclick="upload()">上传</div>
                <div class="warn"><span class="warn-logo"></span>权限开放~维护靠大家</div>
            </div>
            <div class="list-down">
                <div class="public">公告栏</div>
                <div class="content">
                    <p>各位宝贝~新年快乐啊~</p>
                    <p>产品经理露出了和善的微笑…</p>
                    <p>这个平台吼不吼哇~</p>
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

        function upload()
        {
            $("#myUpload").modal('show');
        }
    </script>
@endsection