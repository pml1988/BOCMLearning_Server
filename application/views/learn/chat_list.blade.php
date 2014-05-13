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
                        <a class="btn btn-inverse" href="{{URL::to('learn/chat_list?id='.Input::get('id'))}}">刷新</a>
                    </li>
                    <li>
                        <a class="btn btn-danger" href="{{URL::to('learn/chat_del?gid='.Input::get('id'))}}">清空全部</a>
                    </li>
                </ul>
            </header>
            <section>
                <form method="get" style="margin: 0;background: none;border: none">
                    <div class="row-fluid">
                        <input type="hidden" name="id" value="{{Input::get('id')}}">
                        <div class="span3">
                            <span>姓名</span>
                            <input class="span8" type="text" name="name" value="{{Input::get('name')}}">
                        </div>
                        <div class="span4">
                            <span>关键词</span>
                            <input class="span8" type="text" name="keyword" value="{{Input::get('keyword')}}">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-inverse pull-right">检索</button>
                        </div>
                    </div>
                </form>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>头像</th>
                        <th>工号</th>
                        <th>姓名</th>
                        <th style="width: 300px;">消息内容</th>
                        <th>发送时间</th>
                        <th>状态</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td><img src="{{$row->avatar}}" width="20px" height="20px"></td>
                        <td>{{$row->user->job_code}}</td>
                        <td>{{$row->name}}</td>
                        <td>{{$row->content}}</td>
                        <td>{{$row->created}}</td>
                        <td><span class="badge {{$row->status == 0 ? 'badge-important' : ''}}">
                                {{$row->status == 0 ? '已删除' : '可见'}}
                            </span></td>
                        <td class="toolbar">
                            <div class="btn-group">
                                @if($row->status == 1)
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('learn/chat_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
                                @else
                                <a class="btn btn-flat" title="恢复" onclick="return confirm('确定要恢复吗?')" href="{{URL::to('learn/chat_recover?id='.$row->id)}}"><span class=" awe-share-alt"></span></a>
                                @endif
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