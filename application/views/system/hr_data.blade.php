@layout('layout.common')

@section('content')
<link rel="stylesheet" href="/js/plugins/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="/js/plugins/kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="/js/plugins/kindeditor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function(K) {
        var uploadbutton = K.uploadbutton({
            button : K('#upload')[0],
            fieldName : 'imgFile',
            url : '{{URL::to("upload/upload_hr?dir=file")}}',
            afterUpload : function(data) {
                if (data.error === 0) {
                    unzip(data.url,$('#zip_password').val());
                } else {
                    alert(data.message);
                }
            },
            afterError : function(str) {
                alert('自定义错误信息: ' + str);
            }
        });
        uploadbutton.fileBox.change(function(e) {
            uploadbutton.submit();
        });
    });
</script>
<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>HR数据导入</h2>
            </header>
            <section>
                <table class="table table-bordered table-hover" style="word-break:break-all;">
                    <tr>
                        <td>上次导入文件</td>
                        <td>上次导入完成时间</td>
                        <td>上次导入状态</td>
                    </tr>
                    <tr>
                        <td>{{Cache::get('last_hr_file','暂无')}}</td>
                        <td>{{Cache::get('last_hr_time','暂无')}}</td>
                        <td>{{Cache::get('last_hr_status','暂无')}}</td>
                    </tr>
                </table>

                <table class="table table-bordered table-hover" style="word-break:break-all;">
                    <tr>
                        <td>当前导入文件</td>
                        <td>当前导入时间</td>
                        <td>当前导入状态</td>
                    </tr>
                    <tr>
                        <td>{{Cache::get('current_hr_file','暂无')}}</td>
                        <td>{{Cache::get('current_hr_time','暂无')}}</td>
                        <td>{{Cache::has('current_hr_status') ? '<span class="loading red" data-original-title="处理中..."></span> '.Cache::get('current_hr_status') : '暂无'}}</td>
                    </tr>
                </table>
                @if(Cache::get('current_hr_status') == '')
                <div class="row-fluid">
                    <div class="span6">
                        <label class="control-label">Zip包密码</label>
                        <div class="controls">
                            <input class="input-medium" type="text" id="zip_password">
                        </div>
                    </div>
                    <div class="span6">
                        <input id="upload" value="上传文件包并导入" />
                        <p class="help-block">注意:填写完密码后点击上传,完成后即将自动在后台进行导入,导入时间较长,可离开此页面或刷新当前页面查看状态</p>
                    </div>
                </div>
                @endif

            </section>
        </div>
    </article>
    <!-- /Data block -->
@endsection

@section('scripts')
<script>
    function unzip(file_name,zip_password)
    {
        var wait_dialog = $.dialog({esc:false,title:'文件包解压缩中...',cancel:false}).lock();

        $.ajax({
            type: "get",
            data:{file_name:file_name,zip_password:zip_password},
            url: "/ajax/unzip",
            success: function(data){
                wait_dialog.close();
                var dialog = $.dialog({
                    title: '文件包解压缩',
                    content: '文件包解压成功!是否开始导入数据?',
                    fixed: true,
                    id: 'unzip',
                    lock: true,
                    cancelVal: '取消',
                    okVal: '开始',
                    ok: function () {
                        do_hr(file_name);
                    },
                    cancel: true
                });
            },
            error: function(){
                wait_dialog.close();
                $.dialog.alert('解压缩失败!请检查密码或压缩包是否正确');
            }
        });
    }

    function do_hr(file_name)
    {
        $.dialog('导入任务已开始!');
        $.ajax({
            type: "get",
            url: "/ajax/do_hr",
            data:{file_name:file_name},
            success: function(data){}
        });
    }
</script>
@endsection