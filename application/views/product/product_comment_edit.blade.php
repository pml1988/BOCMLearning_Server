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
                <form method="post">
                    <fieldset>

                        <div class="control-group">
                            <label class="control-label" for="content">评论内容</label>
                            <div class="controls">
                                <textarea id="content" class="input-xlarge span7" name="content" rows="4">{{$comment->content}}</textarea>
                                <p class="help-block">评论内容,2-256个字以内</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="score">评分</label>
                            <div class="controls">
                                <input id="score" name="score" class="input-xlarge" type="text" value="{{$comment->score}}">
                                <p class="help-block">用户评分,0-5数字</p>
                            </div>
                        </div>
                        <div class="control-group row-fluid">
                            <div class="span6 row">
                                <label class="control-label" for="is_top">是否置顶</label>
                                <div class="controls">
                                    <label class="checkbox">
                                        <input id="is_top" name="is_top" {{$comment->is_top != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                                        置顶
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