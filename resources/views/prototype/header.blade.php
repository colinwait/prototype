<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script href="{{ asset('js/vue.min.js') }}"></script>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="{{ asset('css/prototype/common.css') }}" rel="stylesheet">
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
        <div class="memu"><a class="{{ isset($category) && $category  == 'new-m2o' ? 'selected' : '' }}" href="/prototype/new-m2o">M2O-NEW系列</a></div>
        <div class="memu"><a class="{{isset($category) &&  $category == 'mxu' ? 'selected': '' }}" href="/prototype/mxu">MXU系列</a></div>
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