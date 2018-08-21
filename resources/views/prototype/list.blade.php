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
                <div class="hit">产品经理会悄悄通过这个按钮传新文档</div>
                <div class="upload" onclick="upload()">上传{{ $types[$current_type] }}</div>
                <div class="warn"><span class="warn-logo"></span>权限开放~维护靠大家</div>
            </div>
            <div class="list-down">
                <div class="public">公告栏</div>
                <div class="content">
                    <p>平台开放中&nbsp&nbsp欢迎提意见~</p>
                    <p>有任何产品上的建议可以 @徐焱茹~</p>
                    <p>bug或增加钉钉机器人推送 @卢杨~</p>
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
                        <input type="file" id="file" name="file[]" multiple="multiple"
                               accept=".{{implode(',.', config('prototype.file_types'))}}">
                        <input type="hidden" name="category_type" value="{{ $category or '' }}">
                        <input type="hidden" name="type" value="{{ $current_type or '' }}">
                        {{--<div class="progress">--}}
                            {{--<div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar"--}}
                                 {{--aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">--}}
                                {{--<span class="sr-only">20% Complete</span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-confirm">确定</button>
                            {{--<button type="button" onclick="UploadFile()" class="btn btn-primary btn-confirm">确定</button>--}}
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

        const navH = $('.list-right').offset().top;
        $(window).scroll(function () {
            const scroH = $(this).scrollTop();
            console.log(scroH);
            if (scroH >= navH) {
                $(".list-right").addClass('list-right-fixed');
            } else if (scroH < navH) {
                $(".list-right").removeClass('list-right-fixed');
            }
        });

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

        {{--$(function () {--}}
            {{--$("#file").change(function () {--}}
                {{--$("#progress-bar").css("width", 0);--}}
            {{--});--}}
        {{--});--}}

        {{--// ajax + jQuery上传--}}
        {{--function UploadFile() {--}}
            {{--const xhrOnProgress = function (fun) {--}}
                {{--xhrOnProgress.onprogress = fun; //绑定监听--}}
                {{--//使用闭包实现监听绑--}}
                {{--return function () {--}}
                    {{--//通过$.ajaxSettings.xhr();获得XMLHttpRequest对象--}}
                    {{--const xhr = $.ajaxSettings.xhr();--}}
                    {{--//判断监听函数是否为函数--}}
                    {{--if (typeof xhrOnProgress.onprogress !== 'function')--}}
                        {{--return xhr;--}}
                    {{--//如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去--}}
                    {{--if (xhrOnProgress.onprogress && xhr.upload) {--}}
                        {{--xhr.upload.onprogress = xhrOnProgress.onprogress;--}}
                    {{--}--}}
                    {{--return xhr;--}}
                {{--}--}}
            {{--};--}}

            {{--const file = $("#file")[0].files;--}}
            {{--const form = new FormData();--}}
            {{--form.append('file', file);--}}
            {{--form.append('category_type', '{{ $category or '' }}');--}}
            {{--form.append('type', '{{ $current_type or '' }}');--}}
            {{--form.append("csrf_token", '{{ csrf_token() }}');--}}
            {{--$.ajax({--}}
                {{--type: 'POST',--}}
                {{--url: '/prototype/upload/',--}}
                {{--data: form,--}}
                {{--processData: false,  // 告诉jquery不转换数据--}}
                {{--contentType: false,  // 告诉jquery不设置内容格式--}}
                {{--xhr: xhrOnProgress(function (e) {--}}
                    {{--const percent = e.loaded / e.total;--}}
                    {{--$("#progress-bar").css("width", (percent * 500));--}}
                {{--}),--}}

                {{--success: function (arg) {--}}
                    {{--console.log(arg);--}}
                {{--}--}}
            {{--})--}}
        {{--}--}}
    </script>
@endsection