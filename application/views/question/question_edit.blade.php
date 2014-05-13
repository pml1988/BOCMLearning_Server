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
                            <label class="control-label" for="question_type_id">问题分类</label>
                            <div class="controls">
                                <select id="question_type_id" name="question_type_id">
                                    @foreach( $question_types as $type )
                                    <option {{$type->id == $question->question_type_id ? 'selected="selected"' : ''}} value="{{$type->id}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="input">问题标题</label>
                            <div class="controls">
                                <input id="input" name="title" class="input-xlarge span7" type="text" value="{{$question->title}}">
                                <p class="help-block">问题标题,2-32个字以内</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="content">问题内容</label>
                            <div class="controls">
                                <textarea id="content" class="input-xlarge span7" name="content" rows="4">{{$question->content}}</textarea>
                                <p class="help-block">问题内容,2-256个字以内</p>
                            </div>
                        </div>
                        <div class="control-group row-fluid">
                            <div class="span6 row">
                                <label class="control-label" for="is_suggest">是否推荐</label>
                                <div class="controls">
                                    <label class="checkbox">
                                        <input id="is_suggest" name="is_suggest" {{$question->is_suggest != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                                        推荐
                                    </label>
                                </div>
                            </div>
                            <div class="span6 row">
                                <label class="control-label" for="can_answer">是否允许回复</label>
                                <div class="controls">
                                    <label class="checkbox">
                                        <input id="can_answer" name="can_answer" {{$question->can_answer != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                                        允许回复
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