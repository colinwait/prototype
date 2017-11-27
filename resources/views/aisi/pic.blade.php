@extends('layouts.app')

@section('styles')
    <style>
        a {
            text-decoration: none;
        }

        img {
            height: 650px;
            object-fit: cover;
        }

        .margin-auto {
            margin: 0 auto;
            width: 960px;
            height: 650px;
            margin-bottom: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        @foreach($pics as $pic)
            <div >
                <a href="{{ $pic }}">
                    <div class="margin-auto">
                        <img src="{{ $pic }}">
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{--<div class="container">--}}
        {{--@if ($pre_page)--}}
            {{--<a href="/aisi/{{ $id }}?suits={{ $suits }}&page={{ $pre_page }}">--}}
                {{--<button class="btn-primary"> 上一页 </button>--}}
            {{--</a>--}}
        {{--@endif--}}
            {{--<button class="btn-primary"> {{ $page }} </button>--}}
        {{--@if ($next_page)--}}
            {{--<a href="/aisi/{{ $id }}?suits={{ $suits }}&page={{ $next_page }}">--}}
                {{--<button class="btn-primary"> 下一页 </button>--}}
            {{--</a>--}}
        {{--@endif--}}
    {{--</div>--}}
@endsection

@section('scripts')

@endsection