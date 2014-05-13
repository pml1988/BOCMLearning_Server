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
                    @if(Input::has('top_id'))
                    <li>
                        <a class="btn btn-inverse" href="{{URL::to('product/type_list')}}">返回</a>
                    </li>
                    <li>
                        <a class="btn btn-danger" href="{{URL::to('product/type_add?top_id='.Input::get('top_id'))}}">添加</a>
                    </li>
                    @else
                    <li>
                        <a class="btn btn-danger" href="{{URL::to('product/type_add')}}">添加</a>
                    </li>
                    @endif
                </ul>
            </header>
            <section>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th>排序</th>
                        <th>产品类别名称</th>
                        @if(!Input::has('top_id'))
                        <th>子分类数</th>
                        @endif
                        <th>状态</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->sort}}</td>
                        @if($row->level != 1)
                        <td><a>{{$row->name}}</a></td>
                        @else
                        <td><a title="查看子分类" href="{{URL::to('product/type_list?top_id='.$row->id)}}">{{$row->name}}</a></td>
                        <td>{{$row->level_count}}</td>
                        @endif
                        <td><span class="label {{$row->status == 1 ? 'label-important' : ''}}">
                                {{$row->status == 1 ? '已生效' : '未生效'}}
                            </span></td>
                        <td class="toolbar">
                            <div class="btn-group">
                                <a class="btn btn-flat" title="修改" href="{{URL::to('product/type_edit?id='.$row->id.'&top_id='.$row->top_id)}}"><span class="awe-pencil"></span></a>
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('product/type_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
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