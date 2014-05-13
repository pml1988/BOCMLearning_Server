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

                <div class="row-fluid">
                    <form method="get" style="margin: 0;background: none;border: none">
                        <div class="span3">
                            <span>关键词</span>
                            <input class="span8" type="text" name="search_name" value="{{Input::get('search_name')}}">
                        </div>
                        <div class="span3">
                            <span>分类</span>
                            <select id="product_type" style="width: 150px" name="search_type">
                                <option {{Input::get('search_type') == '' ? 'selected="selected"' : ''}} value="">不限
                                </option>
                                @foreach( $types as $type )
                                <option {{$type->id == Input::get('search_type') ? 'selected="selected"' : ''}} value="{{$type->id}}">{{$type->name}}
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
                        <th class="span1">编号</th>
                        <th class="span2">分类名称</th>
                        <th class="span6">标题</th>
                        <th class="span1">回复</th>
                        <th class="span1">最佳</th>
                        <th class="span1">热度</th>
                        <th class="span2">发布时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->question_type->name}}</td>
                        <td><a title="查看详情" href="{{URL::to('question/answer_list?id='.$row->id.'&ref='.base64_encode(URI::full()))}}">{{$row->title}}</a></td>
                        <td><span class="badge {{$row->answer_count > 0 ? 'badge-important' : ''}}">
                                {{$row->answer_count}}
                            </span></td>
                        <td><span class="label {{$row->had_best === true ? 'label-important' : ''}}">
                                {{$row->had_best === true ? '已有' : '暂无'}}
                            </span></td>
                        <td><span class="badge {{$row->hot > 0 ? 'badge-important' : ''}}">
                                {{Util::short_number($row->hot)}}
                            </span></td>
                        <td>{{$row->created_at}}</td>
                        <td class="toolbar">
                            <div class="btn-group">
                                @if($row->is_suggest == 0)
                                <a class="btn btn-flat" title="设为推荐" href="{{URL::to('question/question_suggest?id='.$row->id)}}"><span class="awe-thumbs-up"></span></a>
                                @else
                                <a class="btn btn-flat btn-inverse" title="取消推荐" href="{{URL::to('question/question_suggest?id='.$row->id)}}"><span class="awe-thumbs-down"></span></a>
                                @endif
                                <a class="btn btn-flat" title="修改" href="{{URL::to('question/question_edit?id='.$row->id.'&ref='.base64_encode(URI::full()))}}"><span class="awe-pencil"></span></a>
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('question/question_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
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