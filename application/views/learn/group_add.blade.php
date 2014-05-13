@layout('layout.common')

@section('content')
<link rel="stylesheet" href="/js/plugins/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="/js/plugins/kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="/js/plugins/kindeditor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function(K) {
        var editor = K.editor({
            allowFileManager : true,
            fileManagerJson : '{{URL::to("upload/upload_file_manager")}}',
            uploadJson : '{{URL::to("upload/upload")}}'
        });

        K('#add_file').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    showRemote : false,
                    imageUrl : K('#url').val(),
                    clickFn : function(url, title) {
                        $('#file_list').html('' +
                            '<img src="'+url+'">' +
                            '<input type="hidden" name="icon_url" value="'+url+'">');
                        $('#del_file').show();
                        editor.hideDialog();
                    }
                });
            });
        });
    });

    function del_file()
    {
        $('#file_list').empty();
        $('#del_file').hide();
    }
</script>

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
                <form method="post" class="form-horizontal" >
                    <fieldset>
                        <div class="control-group">
                            <label class="control-label" for="input">学习小组名称</label>
                            <div class="controls">
                                <input id="input" name="name" class="input-xlarge" type="text" value="{{Input::old('name')}}">
                                <p class="help-block">学习小组名称,2-16个字以内</p>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="num">讨论组群号</label>
                            <div class="controls">
                                <input id="num" name="num" class="input-xlarge" type="text" value="{{Input::old('num',mt_rand(10000000,99999999))}}">
                                <p class="help-block">默认为随机数,可以修改为:8位以内,英文加数字</p>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="num">详细描述</label>
                            <div class="controls">
                                <textarea name="detail"></textarea>
                                <p class="help-block">该学习小组的描述信息,512字以内</p>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="video_list">学习小组图标</label>
                            <div class="controls">
                                <ul class="thumbnails" id="video_list">
                                    <li id="file_list"></li>
                                </ul>
                                <a class="btn btn-danger" id="add_file">添加图标</a>
                                <a onclick="del_file()" class="btn btn-inverse" id="del_file" style="display: none">删除</a>
                                <p class="help-block">如不上传则使用默认头像,上传后将会进行适当的裁剪</p>
                            </div>
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

@endsection