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
                        <a class="btn btn-danger" href="{{URL::to('system/version_add')}}">添加新版本</a>
                    </li>
                </ul>
            </header>
            <section>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th>版本号</th>
                        <th>更新说明</th>
                        <th>发布日期</th>
                        <th>是否强制</th>
                        <th>状态</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i=1?>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->version}}</td>
                        <td style="max-width: 300px;"><a>{{nl2br($row->content)}}</a></td>
                        <td>{{$row->created_at}}</td>
                        <td><span class="label {{$row->is_force == 1 ? 'label-important' : ''}}">
                                {{$row->is_force == 1 ? '强制' : '非强制'}}
                            </span></td>
                        @if($i == 1)
                        <td><span class="label label-success">
                                最新
                            </span></td>
                        @else
                        <td><span class="label">
                                老版本
                            </span></td>
                        @endif
                        <td class="toolbar">
                            <div class="btn-group">
                                <a class="btn btn-flat" title="修改" href="{{URL::to('system/version_edit?id='.$row->id)}}"><span class="awe-pencil"></span></a>
                                <a class="btn btn-flat" title="删除" onclick="return confirm('确定要删除吗?')" href="{{URL::to('system/version_del?id='.$row->id)}}"><span class="awe-remove"></span></a>
                            </div>
                        </td>
                    </tr>
                    <?php $i++?>
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