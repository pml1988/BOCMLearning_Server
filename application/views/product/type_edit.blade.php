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
                        <a class="btn btn-inverse" href="{{URL::to(Input::has('top_id') ? 'product/type_list?top_id='.Input::get('top_id') : 'product/type_list')}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
                <form method="post">
                    <fieldset>
                        <div class="control-group">
                            <label class="control-label" for="input">类型名称</label>
                            <div class="controls">
                                <input id="input" name="name" class="input-xlarge" type="text" value="{{$type->name}}">
                                <p class="help-block">产品类型名称,2-16个字以内</p>
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
                        @if(Input::has('top_id'))
                        <div class="control-group">
                            <label class="control-label" for="top_id">上级分类</label>
                            <div class="controls">
                                <select id="top_id" name="top_id">
                                    @foreach( $top_types as $top_type )
                                    <option {{$top_type->id == Input::get('top_id') ? 'selected="selected"' : ''}} value="{{$top_type->id}}">{{$top_type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
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