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
            editor.loadPlugin('insertfile', function() {
                editor.plugin.fileDialog({
                    fileUrl : K('#url').val(),
                    clickFn : function(url, title) {
                        $('#file_list').html('' +
                            '<a id="file_url" target="_blank" href="'+url+'">下载地址</a>' +
                            '<input type="hidden" name="download_url" value="'+url+'">');
                        editor.hideDialog();
                    }
                });
            });
        });
    });
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
                        <a class="btn btn-inverse" href="{{URL::to('system/version_list')}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
                <form method="post">
                    <fieldset>
                        <div class="control-group">
                            <label class="control-label" for="content">更新说明</label>
                            <div class="controls">
                                <textarea id="content" name="content" rows="4">{{Input::old('content'),$version->content}}</textarea>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="optionsCheckbox">是否强制升级</label>
                            <div class="controls">
                                <label class="checkbox">
                                    <input id="optionsCheckbox" name="is_force" {{Input::old('is_force',$version->is_force) != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                                    强制升级
                                </label>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="version">版本号</label>
                            <div class="controls">
                                <input id="version" name="version" class="input-xlarge" type="text" value="{{Input::old('version',$version->version)}}">
                                <p class="help-block">如v1.0等,可以含有文字</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="version_code">版本更新戳</label>
                            <div class="controls">
                                <input id="version_code" name="version_code" class="input-xlarge" type="text" value="{{Input::old('version_code',$version->version_code)}}">
                                <p class="help-block">需与程序包内一致,请询开发人员</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="video_list">程序包上传</label>
                            <div class="controls">
                                <ul class="thumbnails" id="video_list">
                                    <li id="file_list">
                                        <a id="file_url" target="_blank" href="{{Input::old('download_url',$version->download_url)}}">下载地址</a>
                                        <input type="hidden" name="download_url" value="{{Input::old('download_url',$version->download_url)}}">
                                    </li>
                                </ul>
                                <a class="btn btn-danger" id="add_file">上传</a>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button class="btn btn-large btn-danger" version="submit">保存</button>
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