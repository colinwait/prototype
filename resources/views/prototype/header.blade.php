<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>小鳄鱼的原型分享</title>
    <script href="{{ asset('js/vue.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="{{ asset('css/prototype/common.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('/images/prototype/favicon.ico') }}"/>
    @yield('styles')
</head>
<body>
<div id="header" class="container clearfix">
    <a href="/prototype">
        <div class="logo">
            <div class="logo-name">SCHNAPPI</div>
            <div class="logo-display">小 鳄 鱼 的 原 型 分 享</div>
        </div>
    </a>
    <div class="p-nav clearfix">
        @foreach($categories as $index => $_category)
            <div class="memu"><a class="{{ isset($category) && $category  == $index ? 'selected' : '' }}"
                                 href="/prototype/{{ $index }}">{{ $_category }}</a></div>
        @endforeach
    </div>
    <div class="search" @if( !isset($category)) style="display: none" @endif>
        <form class="bs-example bs-example-form" role="form" method="get" action="/prototype/{{ $category or '' }}">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="搜索{{ $types[$current_type] ?? ''}}">
                <input type="hidden" class="form-control" name="type" value="{{ $current_type ?? ''}}">
                <span class="input-group-btn">
						<button class="btn btn-default" type="submit">
						</button>
					</span>
            </div>
        </form>
    </div>
</div>
@yield('content')


<div id="footer">
    <div class="text text-center">Design by yuffie & colin</div>
    <div class="text text-center">2018 hogesoft</div>
</div>
@yield('scripts')
</body>
</html>