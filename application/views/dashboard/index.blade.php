@layout('layout.common')

@section('content')

<div class="row">

    <!-- Data block -->
    <article class="span7 data-block raw">
        <div class="data-container">
            <header>
                <h2>控制台</h2>
                <h2 class="pull-right label label-important">{{$login_time}}</h2><h2 class="pull-right">上次登陆&nbsp;&nbsp;</h2>
            </header>
                <footer class="info">
                    <p style="font-size: 16px">{{Auth::user()->user_name}},欢迎登录江苏中行产品知识管理平台</p>
                </footer>
                <p>您的角色为:
                    <?php
                    if(Auth::user()->roles == null) echo '<span title="普通用户" class="label">普通用户</span>';
                    else
                        foreach(unserialize(Auth::user()->roles) as $role)
                        {
                            switch($role)
                            {
                                case 'root' :
                                    echo '<span title="超级管理员" class="label label-important">超级管理员</span>';
                                    break;
                                case 'admin' :
                                    echo '<span title="系统管理员" class="label label-important">系统管理员</span>';
                                    break;
                                case 'product' :
                                    echo '<span title="产品管理员" class="label label-inverse">产品管理员</span>';
                                    break;
                                case 'question' :
                                    echo '<span title="问答管理员" class="label label-info">问答管理员</span>';
                                    break;
                                case 'learn' :
                                    echo '<span title="学习管理员" class="label label-success">学习管理员</span>';
                                    break;
                            }
                        }
                    ?>
                </p>
                <p>下方列出的是您的管理权限与职责,如有疑问请联系管理员</p>
                <div class="data-container">
                    <div id="accordionName" class="accordion">
                        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('system') === true)
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionName" href="#accordionTabOne">系统管理</a>
                            </div>
                            <div id="accordionTabOne" class="accordion-body in collapse" style="height: auto;">
                                <div class="accordion-inner">可修改系统配置,查看意见反馈,发布客户端版本更新等功能</div>
                            </div>
                        </div>
                        @endif
                        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('product') === true)
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionName" href="#accordionTabTwo">产品管理</a>
                            </div>
                            <div id="accordionTabTwo" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">可进行产品分类管理,产品详情字段管理,产品标签管理,产品内容管理,产品评论管理等</div>
                            </div>
                        </div>
                        @endif
                        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('question') === true)
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionName" href="#accordionTabThree">问答管理</a>
                            </div>
                            <div id="accordionTabThree" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">可针对当前权限进行问答分类管理,问题及回复管理,并可以设置最佳答案等</div>
                            </div>
                        </div>
                        @endif
                        @if(Bouncer::investigate(Auth::user())->allow_or_block_on('learn') === true)
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionName" href="#accordionTabFour">学习管理</a>
                            </div>
                            <div id="accordionTabFour" class="accordion-body collapse" style="height: 0px;">
                                <div class="accordion-inner">可根据管理权限建立学习小组并发布学习任务</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            <div class="row">
                <table class="table table-striped">
                    <thead><tr><th>服务器变量</th><th>属性值</th></tr></thead>
                    <tr><td>PHP脚本解释器版本：</td><td><?PHP echo PHP_VERSION; ?></td></tr>
                    <tr><td>ZEND版本：</td><td><?PHP echo zend_version(); ?></td></tr>
                    <tr><td>MYSQL支持：</td><td><?php echo function_exists('mysql_close')?"是":"否"; ?></td></tr>
                    <tr><td>MySQL数据库持续连接：</td><td><?php echo @get_cfg_var("mysql.allow_persistent")?"是":"否"; ?></td></tr>
                    <tr><td>MySQL最大连接数：</td><td><?php echo @get_cfg_var("mysql.max_links")==-1 ? "不限" : @get_cfg_var("mysql.max_links");?></td></tr>
                    <tr><td>服务器操作系统：</td><td><?PHP echo PHP_OS; ?></td></tr>
                    <tr><td>服务器端信息：</td><td><?PHP echo $_SERVER['SERVER_SOFTWARE']; ?></td></tr>
                    <tr><td>最大上传限制：</td><td><?PHP echo get_cfg_var("upload_max_filesize")?get_cfg_var("upload_max_filesize"):"不允许上传附件"; ?></td></tr>

                </table>
            </div>

        </div>
    </article>
    <!-- /Data block -->
    <article class="span5 data-block raw">
        <header>
            <h2>最近管理日志</h2>
        </header>
        @if(count($logs) > 0)
        <table class="table table-stripped table-hover">
            <thead>
            <tr>
                <th>操作</th>
                <th>时间</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{$log->message}}</td>
                <td>{{$log->created_at}}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <p>暂无</p>
        @endif
    </article>

</div>
@endsection

@section('scripts')

@endsection