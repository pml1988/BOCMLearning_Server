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
                        <a class="btn btn-inverse" href="{{URL::to('learn/group_list')}}">返回</a>
                    </li>
                    <li>
                        <a class="btn btn-danger" href="{{URL::to('learn/group_user_add?group_id='.Input::get('id'))}}">添加成员</a>
                    </li>
                </ul>
            </header>
            <section>
                <a class="btn" href="{{URL::to('learn/group_user_list?id='.Input::get('id'))}}">查看全部</a>
                <a class="btn" href="{{URL::to('learn/group_user_list?id='.Input::get('id')).'&s=origin'}}">只查看固定成员</a>
                <a class="btn" href="{{URL::to('learn/group_user_list?id='.Input::get('id')).'&s=free'}}">只查看自由加入成员</a>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>工号</th>
                        <th>姓名</th>
                        <th>固定/自由</th>
                        <th>群聊权限</th>
                        <th>添加时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->user->job_code}}</td>
                        <td>{{$row->user->user_name}}</td>
                        <td><span class="label {{$row->is_freedom == 0 ? 'label-important' : ''}}">
                                {{$row->is_freedom == 0 ? '固定' : '自由'}}
                            </span></td>
                        <td><span class="label {{$row->chatable == 1 ? 'label-important' : ''}}">
                                {{$row->chatable == 1 ? '允许群聊' : '禁止群聊'}}
                            </span></td>
                        <td>{{$row->created_at}}</td>
                        <td class="toolbar">
                            <div class="btn-group">
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('learn/group_user_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
                            </div>

                            @if($row->chatable == 1)
                            <div class="btn-group">
                                <a class="btn btn-flat" title="设为禁止群聊" href="{{URL::to('learn/group_user_chatable?id='.$row->id)}}"><span class="awe-ban-circle"></span></a>
                            </div>
                            @else
                            <div class="btn-group">
                                <a class="btn btn-flat" title="设为允许群聊" href="{{URL::to('learn/group_user_chatable?id='.$row->id)}}"><span class="awe-comments-alt"></span></a>
                            </div>
                            @endif
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