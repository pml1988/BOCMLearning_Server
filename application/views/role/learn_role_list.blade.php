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
                        <a class="btn btn-danger" href="{{URL::to('role/learn_role_add')}}">添加</a>
                    </li>
                </ul>
            </header>
            <section>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>工号</th>
                        <th>姓名</th>
                        <th>添加人</th>
                        <th>管理范围</th>
                        <th>添加时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->user->job_code}}</td>
                        <td>{{$row->user->user_name}}</td>
                        <td>{{$row->admin->user_name}}</td>
                        <td>{{$row->user->area}}</td>
                        <td>{{$row->created_at}}</td>
                        <td class="toolbar">
                            <div class="btn-group">
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('role/learn_role_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
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