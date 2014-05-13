@layout('layout.common')

@section('content')

<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>{{$web_title}}</h2>
                <ul class="data-header-actions">
                    <li>
                        <a class="btn btn-danger" href="{{URL::to('learn/group_add')}}">添加小组</a>
                    </li>
                </ul>
            </header>
            <section>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>图标</th>
                        <th>群号</th>
                        <th>小组名称</th>
                        <th>成员数</th>
                        <th>任务数</th>
                        <th>管理员</th>
                        <th>添加时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td><img src="{{$row->icon_url}}" width="20px" height="20px"></td>
                        <td>{{$row->num}}</td>
                        <td><a title="查看小组成员" href="{{URL::to('learn/group_user_list?id='.$row->id)}}">{{$row->name}}</a></td>
                        <?php $user_count = UserGroup::where('group_id','=',$row->id)->count();?>
                        <td><span class="badge {{$user_count > 0 ? 'badge-important' : ''}}">
                                {{$user_count}}
                            </span></td>
                        <?php $task_count = UTask::where('group_id','=',$row->id)->count();?>
                        <td><span class="badge {{$task_count > 0 ? 'badge-important' : ''}}">
                                {{$task_count}}
                            </span></td>
                        <td>{{$row->admin->user_name}}</td>
                        <td>{{$row->created_at}}</td>
                        <td class="toolbar">
                            <div class="btn-group">
                                <a class="btn btn-flat" title="添加任务" href="{{URL::to('learn/task_add?id='.$row->id)}}"><span class="awe-book"></span></a>
                                <a class="btn btn-flat" title="群聊记录" href="{{URL::to('learn/chat_list?id='.$row->id)}}"><span class="awe-comments-alt"></span></a>
                                <a class="btn btn-flat" title="成员管理" href="{{URL::to('learn/group_user_list?id='.$row->id)}}"><span class="awe-user"></span></a>
                                <a class="btn btn-flat" title="修改小组信息" href="{{URL::to('learn/group_edit?id='.$row->id)}}"><span class="awe-pencil"></span></a>
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('learn/group_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
                            </div>
                        </td>
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
@endsection