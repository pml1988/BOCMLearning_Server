<!DOCTYPE html>
<!--
 ____   ___   ____       _ ____
| __ ) / _ \ / ___|     | / ___|
|  _ \| | | | |      _  | \___ \
| |_) | |_| | |___  | |_| |___) |
|____/ \___/ \____|  \___/|____/

-->
<!--[if IE 8]>    <html class="no-js ie8 ie" lang="en"> <![endif]-->
<!--[if IE 9]>    <html class="no-js ie9 ie" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>
        @if(isset($web_title))
        {{$web_title.' | '}}
        @endif
        {{Config::get('site.site_name')}}
    </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS styles -->
    <link rel='stylesheet' type='text/css' href='/css/common.css'>

    <!-- Fav and touch icons -->
    <link rel="shortcut icon" href="/favicon.ico">

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- JS Libs -->
    <script src="/js/libs/jquery.js"></script>
    <script src="/js/libs/modernizr.js"></script>
    <script src="/js/libs/selectivizr.js"></script>
    <script>
        $(document).ready(function(){
            $('[title]').tooltip({
                placement: 'top'
            });

        });
    </script>
</head>
<body>

<!-- Main page header -->
<header class="container">

    <!-- Main page logo -->
    <h1><a href="{{URL::base()}}"  class="brand">产品知识管理平台</a></h1>

    <!-- Main page headline -->
    <p>产品知识管理平台</p>

    <!-- Alternative navigation -->
    <nav>
        <ul>
            <li>
                <div class="btn-group">
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                        常用功能
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{URL::to('dashboard')}}"><span class="awe-flag"></span> 控制台</a></li>
                        <li><a href="{{URL::to('statistic/traffic')}}"><span class="awe-flag"></span> 访问统计</a></li>
                        <li><a href="{{URL::to('statistic/product')}}"><span class="awe-flag"></span> 产品统计</a></li>
                        <li><a href="{{URL::to('statistic/admin_log_list')}}"><span class="awe-flag"></span> 管理日志</a></li>
                    </ul>
                </div>
            </li>
            <li><a href="{{URL::to('logout')}}">退出登录</a></li>
        </ul>
    </nav>
    <!-- /Alternative navigation -->

</header>
<!-- /Main page header -->

<!-- Main page container -->
<section class="container" role="main">

<!-- Left (navigation) side -->
<div class="navigation-block">

    <!-- User profile -->
    <section class="user-profile">
        <figure>
            <img alt="管理员" src="{{Auth::user()->avatar_url}}">
            <figcaption>
                <strong><a href="#"  class="">{{Auth::user()->user_name}}</a></strong>
                <em>{{Util::get_role_names(unserialize(Auth::user()->roles))}}</em>
                <ul>
                    <li><a class="btn btn-primary btn-flat" href="{{URL::to('logout')}}" title="退出登录">退出登录</a></li>
                </ul>
            </figcaption>
        </figure>
    </section>
    <!-- /User profile -->

    <!-- Main navigation -->
    {{render('layout.left_nav')}}
    <!-- /Main navigation -->

    <!-- Sample side note -->
    <section class="side-note">
        <div class="side-note-container">
            <h2>平台公告</h2>
            <p>请使用IE7以上,或Chrome等非IE内核的浏览器来访问该平台</p>
        </div>
        <div class="side-note-bottom"></div>
    </section>
    <!-- /Sample side note -->

</div>
<!-- Left (navigation) side -->

<!-- Right (content) side -->
<div class="content-block" role="main">
    {{Messages::get_html()}}
    @yield('content')
</div>
<!-- /Right (content) side -->

</section>
<!-- /Main page container -->

<!-- Main page footer -->
<footer class="container">
    <ul>
        <li><a href="#" class="">江苏中行-产品知识管理平台</a></li>
    </ul>

    <p style="padding-left:10px"> 技术支持 <a href="http://www.iiseeuu.com" target="_blank">南京爱西柚网络科技有限公司</a></p>

    <a href="javascript:void(0)" onclick="$('body,html').animate({scrollTop:0},300);" class="btn btn-primary btn-flat pull-right">回顶部 &uarr;</a>
</footer>
<!-- /Main page footer -->

<!-- Scripts -->
<script src="/js/navigation.js"></script>
<script src="/js/bootstrap/bootstrap-affix.js"></script>
<script src="/js/bootstrap/bootstrap-alert.js"></script>
<script src="/js/bootstrap/bootstrap-tooltip.js"></script>
<script src="/js/bootstrap/bootstrap-collapse.js"></script>
<script src="/js/bootstrap/bootstrap-dropdown.js"></script>
<script src="/js/bootstrap/bootstrap-transition.js"></script>

<script src="/js/plugins/artDialog/jquery.artDialog.js?skin=black"></script>
<script src="/js/plugins/artDialog/plugins/iframeTools.js"></script>


@yield('scripts')

</body>
</html>