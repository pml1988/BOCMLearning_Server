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

    <h1>403</h1>
    <p class="description">您没有权限查看当前页面</p>
    <p>你的账号权限不足,如有问题请联系管理员</p>
    <a href="{{URL::base()}}" class="btn btn-alt btn-primary btn-large" title="回到首页">回到首页</a>

</section>
<!-- /Error page container -->

<!-- Bootstrap scripts -->
<script src="/js/bootstrap/bootstrap-tooltip.js"></script>

</body>
</html>
