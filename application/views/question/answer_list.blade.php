@layout('layout.common')

@section('content')
<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>{{$question->title}}</h2>
                <ul class="data-header-actions">
                    <li>
                        <a class="btn btn-inverse" href="{{URL::to(base64_decode(Input::get('ref')))}}">返回</a>
                    </li>
                </ul>
            </header>
            <p>{{$question->content}}</p>
        </div>
    </article>
    <!-- /Data block -->


</div>
<!-- /Grid row -->

<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>回答列表</h2>
            </header>

            <section>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th class="span5">回答</th>
                        <th>最佳</th>
                        <th>姓名</th>
                        <th>回答时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <th>{{$row->id}}</th>
                        <td>{{nl2br($row->content)}}</td>
                        <td><span class="label {{$row->is_best == 1 ? 'label-important' : ''}}">
                                {{$row->is_best == 1 ? '是' : '否'}}
                            </span></td>
                        <td>{{$row->user->user_name}}</td>
                        <td>{{$row->created_at}}</td>
                        <td class="toolbar">
                            <div class="btn-group">
                                @if($row->is_best == 0)
                                <a class="btn btn-flat" title="设为最佳答案" href="{{URL::to('question/answer_best?id='.$row->id)}}"><span class="awe-thumbs-up"></span></a>
                                @else
                                <a class="btn btn-flat" title="取消最佳答案" href="{{URL::to('question/answer_best?id='.$row->id)}}"><span class="awe-thumbs-down"></span></a>
                                @endif
                                <a class="btn btn-flat" title="修改" href="{{URL::to('question/answer_edit?id='.$row->id.'&ref='.base64_encode(URI::full()))}}"><span class="awe-pencil"></span></a>
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('question/answer_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
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