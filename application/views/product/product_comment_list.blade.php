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
                        <a class="btn btn-inverse" href="{{URL::to(base64_decode(Input::get('ref')))}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th class="span6">评论内容</th>
                        <th>评分</th>
                        <th>发布者</th>
                        <th>时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->id}}</td>
                        <td><a>{{nl2br($row->content)}}</a></td>
                        <td><span class="badge {{$row->score > 0 ? 'badge-important' : ''}}">
                                {{$row->score}}
                            </span></td>
                        <td>{{$row->user->user_name}}</td>
                        <td>{{$row->created_at}}</td>
                        <td class="toolbar">
                            <div class="btn-group">
                                @if($row->is_top == 0)
                                <a class="btn btn-flat" title="设为置顶" href="{{URL::to('product/product_comment_top?id='.$row->id)}}"><span class="awe-thumbs-up"></span></a>
                                @else
                                <a class="btn btn-flat btn-inverse" title="取消置顶" href="{{URL::to('product/product_comment_top?id='.$row->id)}}"><span class="awe-thumbs-down"></span></a>
                                @endif
                                <a class="btn btn-flat" title="修改" href="{{URL::to('product/product_comment_edit?id='.$row->id.'&ref='.base64_encode(URI::full()))}}"><span class="awe-pencil"></span></a>
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('product/product_comment_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
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