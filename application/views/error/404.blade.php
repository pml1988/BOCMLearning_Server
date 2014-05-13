<!DOCTYPE html>
<!--[if IE 8]>    <html class="no-js ie8 ie" lang="en"> <![endif]-->
<!--[if IE 9]>    <html class="no-js ie9 ie" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>发生错误</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS styles -->
    <link rel='stylesheet' type='text/css' href='/css/common.css'>
    <link rel="shortcut icon" href="/favicon.ico">

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
<body class="error-page">

<!-- Error page container -->
<section class="error-container">

    <h1>404</h1>
    <p class="description">未找到该页面的内容</p>
    <p>该页面已不存在或您浏览的页面路径有误,如有问题请联系管理员</p>
    <a href="{{URL::base()}}" class="btn btn-alt btn-primary btn-large" title="回到首页">回到首页</a>

</section>
<!-- /Error page container -->

<!-- Bootstrap scripts -->
<script src="/js/bootstrap/bootstrap-tooltip.js"></script>

</body>
</html>
