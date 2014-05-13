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
    <title>登录 | 江苏中行产品知识管理平台</title>
    <meta name="description" content="">
    <meta name="author" content="www.iiseeuu.com">
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
<body>

<!-- Main page container -->
<section class="container login" role="main">

    <h1>
        <a href="" class="brand">产品知识管理平台</a>
        <p style="float: right;font-size: 18px;margin-top: -23px;">产品知识管理平台</p>
    </h1>


    <div class="data-block">
        <form method="post" action="{{URL::to('login')}}">
            {{Messages::get_html()}}

            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="job_code">工号</label>
                    <div class="controls">
                        <input id="job_code" value="{{Input::old('job_code')}}" type="text" placeholder="请输入您的工号" name="job_code">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="password">密码</label>
                    <div class="controls">
                        <input id="password" type="password" placeholder="请输入您的密码" name="password">
                    </div>
                </div>
                <div class="form-actions">
                    <div class="row-fluid">
                        <a class="btn btn-inverse pull-left" data-toggle="modal" href="#download"><span class="awe-download-alt"></span> 下载手机客户端</a>
                        <button class="btn btn-danger pull-right" type="submit"><span class="awe-signin"></span> 登录</button>

                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <div class="modal fade hide" id="download" style="display: none;" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3>下载手机客户端</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid" style="text-align: center">
                <div class="span6">
                    <img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl=https://itunes.apple.com/cn/app/m-learning/id640029894?mt=8">
                    <a href="https://itunes.apple.com/cn/app/m-learning/id640029894?mt=8" target="_blank" class="large_button" id="apple">
                        <span class="icon"></span>
                        <em>立即下载</em> iPhone
                    </a>
                </div>
                <div class="span6">
                    <img src="https://chart.googleapis.com/chart?chs=170x170&cht=qr&chl={{$android}}">
                    <a href="{{$android}}" target="_blank" class="large_button" id="android">
                        <span class="icon"></span>
                        <em>立即下载</em> Android
                    </a>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-alt" data-dismiss="modal">关闭</a>
        </div>
    </div>



</section>
<!-- /Main page container -->

<!-- Scripts -->
<script src="/js/bootstrap/bootstrap-tooltip.js"></script>
<script src="/js/bootstrap/bootstrap-alert.js"></script>
<script src="/js/bootstrap/bootstrap-modal.js"></script>
<script src="/js/bootstrap/bootstrap-transition.js"></script>
</body>
</html>