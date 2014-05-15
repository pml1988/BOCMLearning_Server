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
                        <a class="btn btn-danger" href="{{URL::to('exam/subject_add')}}">添加科目</a>
                    </li>
                </ul>
            </header>
            <section>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>科目名称</th>
                        <th>添加时间</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->title}}</td>
                        <td>{{$row->created_at}}</td>
                        <td class="toolbar">
                            <div class="btn-group">
                                <a class="btn btn-flat" title="查看科目题库" href="{{URL::to('exam/subject_view?id='.$row->id)}}"><span class="awe-comments-alt"></span></a>
                                <a class="btn btn-flat" title="导入题库" href="{{URL::to('exam/subject_import?id='.$row->id)}}"><span class="awe-user"></span></a>
                                <a class="btn btn-flat" title="修改科目信息" href="{{URL::to('exam/subject_edit?id='.$row->id)}}"><span class="awe-pencil"></span></a>
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('exam/subject_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
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