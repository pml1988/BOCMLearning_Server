@layout('layout.common')

@section('content')

<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>系统设置</h2>
            </header>
            <section>
                <form class="form-horizontal" method="post" action="">
                    <fieldset>
                        <div class="control-group row-fluid">
                            <div class="span6">
                                <label class="control-label">是否开启短信平台</label>
                                <div class="controls">
                                    <input type="radio" name="sms_enable" value = "1" {{Util::get_setting('sms_enable') == 0 ? '' : 'checked="checked"'}}> 开启<br>
                                    <input type="radio" name="sms_enable" value = "0" {{Util::get_setting('sms_enable') == 1 ? '' : 'checked="checked"'}}> 关闭<br>

                                </div>
                            </div>
<!--                            <div class="span6">-->
<!--                                <label class="control-label" for="question_submit">发布问题获得积分</label>-->
<!--                                <div class="controls">-->
<!--                                    <input id="question_submit" name="question_submit" class="input-xlarge span4" type="number" value="{{Util::get_setting('score.question_submit')}}">-->
<!--                                </div>-->
<!--                            </div>-->
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
@endsection

@section('scripts')
@endsection