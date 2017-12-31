@extends('prototype.header')

@section('styles')

@endsection

@section('content')
    <div class="container">
        <p>{{ $message or '' }}</p>
        <form class="form-group" action="/prototype/upload" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="file" name="file">
            <select name="category_type">
                <option value="mxu">MXU1.2</option>
                <option value="new-mxu">MXU1.3</option>
                <option value="new-m2o">新M2O</option>
            </select>
            <button type="submit" class="btn btn-primary">上传</button>
        </form>
    </div>
@endsection

@section('scripts')
@endsection