@layout('layout.common')

@section('content')

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
                <div class="control-group">
                    <label class="control-label">任务期限</label>
                    <div class="controls">
                        <input class="Wdate" id="d1" type="text" name="start_at" value="{{$task->start_at}}" disabled="disabled">
                        至
                        <input class="Wdate" id="d2" type="text" value="{{$task->end_at}}"  disabled="disabled">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">任务提醒</label>
                    <div class="controls">
                        任务结束<input name="notify_before" disabled="disabled" type="number" value="{{Input::old('notify_before',$task->notify_before)}}" style="width: 50px">天前进行提醒
                        <label class="checkbox">
                            <input name="sms_notify" disabled="disabled" {{$task->sms_notify != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                            短信提醒
                        </label>
                        <label class="checkbox">
                            <input name="push_notify" disabled="disabled" {{$task->push_notify != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                            客户端推送提醒
                        </label>
                    </div>
                </div>

                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>产品名称</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $products as $row )
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{$row->product_name}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </section>
        </div>
    </article>
    <!-- /Data block -->


</div>
<!-- /Grid row -->
@endsection