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
                        <a class="btn btn-inverse" href="{{base64_decode(Input::get('ref'))}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
                <form method="post">
                    <fieldset>

                        <div class="control-group">
                            <label class="control-label" for="content">回答内容</label>
                            <div class="controls">
                                <textarea id="content" class="input-xlarge span7" name="content" rows="4">{{$answer->content}}</textarea>
                                <p class="help-block">回答内容,2-256个字以内</p>
                            </div>
                        </div>
                        <div class="control-group row-fluid">
                            <div class="span6 row">
                                <label class="control-label" for="is_best">是否最佳答案</label>
                                <div class="controls">
                                    <label class="checkbox">
                                        <input id="is_best" name="is_best" {{$answer->is_best != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                                        最佳答案
                                    </label>
                                </div>
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