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
                        <a class="btn btn-danger" href="{{URL::to('product/product_add')}}">添加</a>
                    </li>
                </ul>
            </header>
            <section>
                <div class="row-fluid">
                    <form method="get" style="margin: 0;background: none;border: none">
                    <div class="span3">
                        <span>产品名称</span>
                        <input class="span8" type="text" name="search_name" value="{{Input::get('search_name')}}">
                    </div>
                    <div class="span3">
                        <span>分类</span>
                        <select id="product_type" style="width: 150px" name="search_type">
                            <option {{Input::get('search_type') == '' ? 'selected="selected"' : ''}} value="">不限
                            </option>
                            @foreach( $types as $type )
                            <option {{$type->id == Input::get('search_type') ? 'selected="selected"' : ''}} value="{{$type->id}}">{{$type->top_name->name.'-'.$type->name}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="span5">
                        <span>发布时间</span>
                        <input class="span4 Wdate" id="d1" onclick="WdatePicker({maxDate:'#F{$dp.$D(\'d2\')}'})" type="text" name="search_start" value="{{Input::get('search_start')}}">
                        至
                        <input class="span4 Wdate" id="d2" onclick="WdatePicker({minDate:'#F{$dp.$D(\'d1\')}'})" type="text" name="search_end" value="{{Input::get('search_end')}}">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-inverse pull-right">检索</button>
                    </div>
                    </form>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th>排序</th>
                        <th>产品名称</th>
                        <th>所属分类</th>
                        <th>状态</th>
                        <th>评论</th>
                        <th>评分</th>
                        <th>热度</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <?php $comment_count = $row->comment_count?>
                    <?php $avg = ceil($row->product_comment()->where('score','!=',0)->avg('score'))?>
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->sort}}</td>
                        <td><a href="{{URL::to('product/product_comment_list?id='.$row->id.'&ref='.base64_encode(URI::full()))}}" title="查看评论({{$comment_count}})">{{$row->product_name}}</a></td>
                        <td>{{$row->product_type->top_name->name.'-'.$row->product_type->name}}</td>
                        <td><span class="label {{$row->status == 1 ? 'label-important' : ''}}">
                                {{$row->status == 1 ? '已生效' : '未生效'}}
                            </span></td>
                        <td><span class="badge {{$comment_count > 0 ? 'badge-important' : ''}}">
                                {{$comment_count}}
                            </span></td>
                        <td><span class="badge {{$avg > 0 ? 'badge-important' : ''}}">
                                {{$avg}}
                            </span></td>
                        <td><span class="badge {{$row->hot > 0 ? 'badge-important' : ''}}">
                                {{Util::short_number($row->hot)}}
                            </span></td>
                        <td class="toolbar">
                            <div class="btn-group">
                                @if($row->is_suggest == 0)
                                <a class="btn btn-flat" title="设为推荐" href="{{URL::to('product/product_suggest?id='.$row->id)}}"><span class="awe-thumbs-up"></span></a>
                                @else
                                <a class="btn btn-flat btn-inverse" title="取消推荐" href="{{URL::to('product/product_suggest?id='.$row->id)}}"><span class="awe-thumbs-down"></span></a>
                                @endif
                                <a class="btn btn-flat" title="修改" href="{{URL::to('product/product_edit?id='.$row->id.'&ref='.base64_encode(URI::full()))}}"><span class="awe-pencil"></span></a>
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('product/product_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
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
<script src="/js/plugins/My97DatePicker/WdatePicker.js"></script>
@endsection