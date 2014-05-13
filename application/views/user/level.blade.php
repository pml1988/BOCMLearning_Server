@layout('layout.common')

@section('content')

<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>积分设置</h2>
            </header>
            <section>
                <form class="form-horizontal" method="post" action="{{URL::to('user/score_edit')}}">
                    <fieldset>
                        <div class="control-group row-fluid">
                            <div class="span6">
                                <label class="control-label" for="comment_submit">发布评论获得积分</label>
                                <div class="controls">
                                    <input id="comment_submit" name="comment_submit" class="input-xlarge span4" type="number" value="{{Util::get_setting('score.comment_submit')}}">
                                </div>
                            </div>
                            <div class="span6">
                                <label class="control-label" for="question_submit">发布问题获得积分</label>
                                <div class="controls">
                                    <input id="question_submit" name="question_submit" class="input-xlarge span4" type="number" value="{{Util::get_setting('score.question_submit')}}">
                                </div>
                            </div>
                        </div>
                        <div class="control-group row-fluid">
                            <div class="span6">
                                <label class="control-label" for="answer_submit">回复问题获得积分</label>
                                <div class="controls">
                                    <input id="answer_submit" name="answer_submit" class="input-xlarge span4" type="number" value="{{Util::get_setting('score.answer_submit')}}">
                                </div>
                            </div>
                            <div class="span6">
                                <label class="control-label" for="best_answer">回复被设为最佳答案获得积分</label>
                                <div class="controls">
                                    <input id="best_answer" name="best_answer" class="input-xlarge span4" type="number" value="{{Util::get_setting('score.best_answer')}}">
                                </div>
                            </div>
                        </div>
                        <div class="control-group row-fluid">
                            <div class="span6">
                                <label class="control-label" for="user_login">每日登录获得积分</label>
                                <div class="controls">
                                    <input id="user_login" name="user_login" class="input-xlarge span4" type="number" value="{{Util::get_setting('score.user_login')}}">
                                </div>
                            </div>
                            <div class="span6">
                                <label class="control-label" for="daily_max">每日用户积分上限</label>
                                <div class="controls">
                                    <input id="daily_max" name="daily_max" class="input-xlarge span4" type="number" value="{{Util::get_setting('score.daily_max')}}">
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

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>等级设置</h2>
            </header>
            <section>
                <form class="form-horizontal" method="post" action="{{URL::to('user/level_edit')}}">
                    <fieldset>
                        @foreach($level as $row)
                        <div class="control-group">
                            <label class="control-label">{{$row->id}}级</label>
                            <div class="controls">
                                称号:
                                <input name="name_{{$row->id}}" id="level_{{$row->id}}" class="input-xlarge span1" type="text" value="{{$row->name}}">
                                 积分:
                                <input name="min_score_{{$row->id}}" id="level_{{$row->id}}" class="input-xlarge span1" type="number" value="{{$row->min_score}}">至
                                <input name="max_score_{{$row->id}}" id="level_{{$row->id}}" class="input-xlarge span1" type="number" value="{{$row->max_score}}">分
                            </div>
                        </div>
                        @endforeach

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