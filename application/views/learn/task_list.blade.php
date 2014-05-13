@layout('layout.common')

@section('content')
<script>

    $(document).ready(function(){
        // Popover
        $('.demoPopover').popover({
            trigger: 'hover',
            placement: 'left'
        });


    });

</script>
<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>{{$web_title}}</h2>
                <ul class="data-header-actions">
                    <li>
                        <a class="btn btn-danger demoPopover" data-content="进入学习小组后点击右侧功能键,针对某一小组发布学习任务" data-original-title="提示" href="{{URL::to('learn/group_list')}}">添加任务</a>
                    </li>
                </ul>
            </header>
            <section>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>任务名称</th>
                        <th>小组名称</th>
                        <th>创建人</th>
                        <th>状态</th>
                        <th>添加时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->title}}</td>
                        <td><a title="查看小组" href="{{URL::to('learn/group_user_list?id='.$row->group_id)}}">{{$row->group->name}}</a></td>
                        <td>{{$row->admin->user_name}}</td>
                        <td><span class="label {{$row->status == 1 ? 'label-important' : ''}}">
                                {{$row->status == 1 ? '已发布' : '未发布'}}
                            </span></td>
                        <td>{{$row->created_at}}</td>
                        <td class="toolbar">
                            <div class="btn-group">
                                <a class="btn btn-flat" title="查看任务详情" href="{{URL::to('learn/task_detail?id='.$row->id.'&ref='.base64_encode(URI::full()))}}"><span class="awe-search"></span></a>
                                @if($row->status == 0)
                                <a class="btn btn-flat" title="修改" href="{{URL::to('learn/task_edit?id='.$row->id.'&ref='.base64_encode(URI::full()))}}"><span class="awe-pencil"></span></a>
                                @else
                                <a class="btn btn-flat" title="完成情况统计" href="{{URL::to('learn/task_status?id='.$row->id.'&ref='.base64_encode(URI::full()))}}"><span class="awe-bar-chart"></span></a>
                                @endif
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('learn/task_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
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
<script src="/js/bootstrap/bootstrap-popover.js"></script>
@endsection