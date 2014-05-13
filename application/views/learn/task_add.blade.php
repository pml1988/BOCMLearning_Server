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
                        <a class="btn btn-inverse" href="{{URL::to('learn/group_list')}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
            <form method="post">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="input">任务名称</label>
                    <div class="controls">
                        <input id="input" name="title" class="input-xlarge" type="text" value="{{Input::old('title')}}">
                        <p class="help-block">任务名称,2-16个字以内</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">是否立即发布任务</label>
                    <div class="controls">
                        <label class="checkbox">
                            <input id="optionsCheckbox" name="status" {{Input::old('status') != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                            立即发布任务
                        </label>
                        <p class="help-block">任务发布后即不可修改只能删除</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">任务期限</label>
                    <div class="controls">
                        <input class="Wdate" id="d1" onclick="WdatePicker({maxDate:'#F{$dp.$D(\'d2\')}'})" type="text" name="start_at" value="{{Input::old('start_at')}}">
                        至
                        <input class="Wdate" id="d2" onclick="WdatePicker({minDate:'#F{$dp.$D(\'d1\')}'})" type="text" name="end_at" value="{{Input::old('end_at')}}">
                        <p class="help-block">只可在任务期限内完成任务(必填)</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">任务提醒</label>
                    <div class="controls">
                        任务结束<input name="notify_before"  type="number" value="5" style="width: 50px">天前进行提醒
                        <label class="checkbox">
                            <input name="sms_notify" {{Input::old('sms_notify') != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                            短信提醒
                        </label>
                        <label class="checkbox">
                        <input name="push_notify" {{Input::old('push_notify') != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                            客户端推送提醒
                        </label>

                        <p class="help-block">注:任务结束前强制提醒,如任务期限小于该天数则不会进行提醒</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">选择学习产品</label>
                    <div class="controls">
                        <div id="accordionName" class="accordion" style="background-color: white;">
                            @foreach($top_types as $top_type)
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionName" href="#accordionTab{{$top_type->id}}">{{$top_type->name}}</a>
                                </div>
                                <div id="accordionTab{{$top_type->id}}" class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        @foreach(ProductType::where('top_id','=',$top_type->id)->where('status','=',1)->get() as $type)
                                        <div class="well row">
                                            <p class="row" style="font-size: 14px;">{{$type->name}}</p>
                                            <div class="row-fluid span10">
                                                @foreach(Product::where('product_type_id','=',$type->id)->where('status','=',1)->get(array('product_name','id')) as $product)
                                                <div class="span3">
                                                    <input type="checkbox" name="product_id[]" value="{{$product->id}}"> {{$product->product_name}}
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                    <p class="help-inline">请适当选择学习产品,最多30个</p>

                </div>
                <div class="form-actions">
                    <button class="btn btn-large btn-danger pull-right" type="submit">保存</button>
                </div>
                    </fieldset>
                </form>
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