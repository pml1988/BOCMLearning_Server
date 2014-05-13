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
                        <a class="btn btn-inverse" href="{{URL::to('question/type_list')}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
                <form method="post">
                    <fieldset>
                        <div class="control-group">
                            <label class="control-label" for="input">问答分类名称</label>
                            <div class="controls">
                                <input id="input" name="name" class="input-xlarge" type="text" value="{{$type->name}}">
                                <p class="help-block">问答分类名称,2-16个字以内</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="optionsCheckbox">是否生效</label>
                            <div class="controls">
                                <label class="checkbox">
                                    <input id="optionsCheckbox" name="status" {{$type->status != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                                    生效
                                </label>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="sort">排序</label>
                            <div class="controls">
                                <input id="sort" name="sort" class="input-xlarge" type="text" value="{{$type->sort}}">
                                <p class="help-block">数值类型,以降序排列,修改即时生效</p>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-large btn-danger" type="submit">保存</button>
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

@endsection