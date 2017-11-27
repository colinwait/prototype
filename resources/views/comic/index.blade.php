@extends('layouts.app')

@section('styles')

@endsection

@section('content')
    <div class="container">
        <p>{{ $message or '' }}</p>
        <form class="form-group" action="comic/download" method="get">
            漫画id <input class="form-control" name="id" value="">
            集数 <input class="form-control" name="collects" value="">
            <button class="btn-primary" type="submit">下载</button>
        </form>
    </div>
@endsection

@section('scripts')

@endsection