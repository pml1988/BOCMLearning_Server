@layout('layout.common')

@section('content')
<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>{{$web_title}}</h2>
            </header>
            <section>
                <form method="get" style="margin: 0;background: none;border: none">
                    <div class="row-fluid">
                        <div class="span3">
                            <span>姓名</span>
                            <input class="span8" type="text" name="search_name" value="{{Input::get('search_name')}}">
                        </div>
                        <div class="span4">
                            <span>工号/EHR号</span>
                            <input class="span8" type="text" name="search_job_code" value="{{Input::get('search_job_code')}}">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-inverse pull-right">检索</button>
                        </div>
                    </div>
<!--                    <div class="accordion">-->
<!--                        <div class="accordion-group">-->
<!--                            <div class="accordion-heading">-->
<!--                                <a class="accordion-toggle collapsed" href="#more" data-parent="#icons" data-toggle="collapse" style="text-align: center"><span class="awe-sort"></span>&nbsp;&nbsp;高级搜索</a>-->
<!--                            </div>-->
<!--                            <div id="more" class="accordion-body collapse" style="height: 0px;">-->
<!--                                <div class="accordion-inner">-->
<!--                                    <p>-->
<!--                                        更多搜索选项-->
<!--                                    </p>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
                </form>

                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>工号/EHR号</th>
                        <th>姓名</th>
                        <th>性别</th>
                        <th>银行/部门</th>
                        <th>积分</th>
                        <th>上次登陆</th>
                        <th>角色</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $user)
                        <tr>
                            <td>{{$user->job_code.' / '.$user->ehr_id}}</td>
                            <td><a title="查看详情" href="javascript:void(0)" onclick="get_detail({{$user->id}})">{{$user->user_name}}</a></td>
                            <td>{{$user->sex == '0' ? '男' : '女'}}</td>
                            <td>{{$user->bank_name}}</td>
                            <td><a>{{$user->score}}</a></td>
                            <td>{{$user->last_login_at}}</td>
                            <td><?php
                                if($user->roles == null) echo '<span title="普通用户" class="label">普</span>';
                                else
                                    foreach(unserialize($user->roles) as $role)
                                    {
                                        switch($role)
                                        {
                                            case 'root' :
                                                echo '<span title="超级管理员" class="label label-important">超</span>';
                                                break;
                                            case 'admin' :
                                                echo '<span title="系统管理员" class="label label-important">系</span>';
                                                break;
                                            case 'product' :
                                                echo '<span title="产品管理员" class="label label-inverse">产</span>';
                                                break;
                                            case 'question' :
                                                echo '<span title="问答管理员" class="label label-info">问</span>';
                                                break;
                                            case 'learn' :
                                                echo '<span title="学习管理员" class="label label-success">学</span>';
                                                break;
                                        }
                                    }
                                ?></td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{$page_link->links()}}

            </section>
        </div>
    </article>
    <!-- /Data block -->


</div>
<!-- /Grid row -->
@endsection

@section('scripts')
<script>
    function get_detail(id)
    {
        var wait_dialog = $.dialog({esc:false,title:'加载中...',cancel:false}).lock();
        $.ajax({
            type: "get",
            data:{id:id},
            url: "/ajax/user_detail",
            success: function(data){
                wait_dialog.close();
                var dialog = $.dialog({
                    title: '用户详情',
                    content: data,
                    fixed: true,
                    padding: '5px 25px',
                    id: 'user_detail_dialog',
                    lock: true,
                    okVal: '关闭',
                    ok: function () {},
                    cancel: false
                });
            },
            error: function(){
                wait_dialog.close();
                $.dialog.alert('系统忙请稍后再试!');
            }
        });
    }
</script>
<script src="/js/plugins/My97DatePicker/WdatePicker.js"></script>
@endsection