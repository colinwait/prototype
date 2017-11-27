@extends('layouts.app')

@section('styles')
    <style>
        a {
            text-decoration: none;
        }

        img {
            width: 250px;
            height: 300px;
            object-fit: cover;
        }

        .float-left {
            float: left;
            width: 250px;
            height: 360px;
            margin-left: 30px;
        }

        #loading img {
            width: 20px;
            height: 20px;
            margin: 0 auto;
        }
    </style>
@endsection

@section('content')
    <div class="container" id="pic_container">
        @foreach($suites as $suite)
            <div class="float-left">
                <a href="/aisi/pic/{{ $suite['issue'] }}?count={{ $suite['pics'] }}&catalog={{ $suite['source']['catalog'] }}">
                    <div class="img-div">
                        <img src="{{ $suite['url'] }}" class="blur">
                        <p>{{ $suite['name'] }}</p>
                        <p>图片总数 {{ $suite['pics'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
        <span id="next_page" style="display: none">{{ $next_page }}</span>
        <span id="id" style="display: none">{{ $id }}</span>
        <span id="is_ajax" style="display: none">0</span>
        <span id="suits" style="display: none">{{ $suits }}</span>
    </div>

    <div class="container">
        {{--<div class="pagination">--}}
        {{--@if ($pre_page)--}}
        {{--<a href="/aisi/{{ $id }}?suits={{ $suits }}&page={{ $pre_page }}">--}}
        {{--<button class="btn-primary"> 上一页</button>--}}
        {{--</a>--}}
        {{--@endif--}}
        {{--<button class="btn-primary"> {{ $page }} </button>--}}
        {{--@if ($next_page)--}}
        {{--<a href="/aisi/{{ $id }}?suits={{ $suits }}&page={{ $next_page }}">--}}
        {{--<button class="btn-primary"> 下一页</button>--}}
        {{--</a>--}}
        {{--@endif--}}
        {{--</div>--}}
        <p style="clear: both"></p>
        <div id='loading' style="display: none">
            <img src="{{ asset('images/loading.gif') }}"/>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/loadmore.js') }}"></script>
@endsection