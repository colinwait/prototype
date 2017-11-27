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
    </style>
@endsection

@section('content')
    <div class="container">
        @foreach($catalogs as $catalog)
            <div class="float-left">
                <a href="/aisi/{{ $catalog['id'] }}?suits={{ $catalog['suits'] }}">
                    <div class="img-div">
                        <img src="{{ $catalog['url'] }}" class="blur">
                        <p>{{ $catalog['name'] }}</p>
                        <p>图片总数 {{ $catalog['pics'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')

@endsection