@layout('layout.common')

@section('content')
<link rel="stylesheet" href="/js/plugins/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="/js/plugins/kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="/js/plugins/kindeditor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function(K) {
        var img_id = 1;
        var editor = K.editor({
            allowFileManager : true,
            fileManagerJson : '{{URL::to("upload/upload_file_manager")}}',
            uploadJson : '{{URL::to("upload/upload")}}'
        });
        K('#add_image').click(function() {
            editor.loadPlugin('image', function() {
                editor.plugin.imageDialog({
                    showRemote : false,
                    imageUrl : K('#img').val(),
                    clickFn : function(url, title, width, height, border, align) {
                        $('#image_list').append('' +
                            '<li id="image_id_'+img_id+'" class="span2">' +
                            '<a class="thumbnail" href="#"><img alt="" id="img" src="'+url+'"></a>' +
                            '<input type="hidden" name="image[]" value="'+url+'">' +
                            '<p style="text-align: center">' +
                            '<a onclick="remove_image('+img_id+')" class="label label-important">删除</a></p></li>');
                        editor.hideDialog();
                        img_id++;
                    }
                });
            });
        });
        K('#add_file').click(function() {
            editor.loadPlugin('insertfile', function() {
                editor.plugin.fileDialog({
                    fileUrl : K('#url').val(),
                    clickFn : function(url, title) {
                        $('#file_list').html('' +
                            '<a id="file_url" target="_blank" href="'+url+'">视频地址</a>' +
                            '<input type="hidden" name="video_url" value="'+url+'">');
                        $('#del_file').show();
                        editor.hideDialog();
                    }
                });
            });
        });

        K('#add_image_from_path').click(function() {
            editor.loadPlugin('filemanager', function() {
                editor.plugin.filemanagerDialog({
                    viewType : 'VIEW',
                    dirName : 'image',
                    clickFn : function(url, title) {
                        $('#image_list').append('' +
                            '<li id="image_id_'+img_id+'" class="span2">' +
                            '<a class="thumbnail" href="#"><img alt="" id="img" src="'+url+'"></a>' +
                            '<input type="hidden" name="image[]" value="'+url+'">' +
                            '<p style="text-align: center">' +
                            '<a onclick="remove_image('+img_id+')" class="label label-important">删除</a></p></li>');
                        editor.hideDialog();
                        img_id++;
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
                        <a class="btn btn-inverse" href="{{URL::to('product/product_list')}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
                <form method="post">
                    <fieldset>
                        <div class="control-group">
                            <label class="control-label" for="product_name">产品名称</label>
                            <div class="controls">
                                <input id="product_name" name="product_name" class="input-xlarge" type="text" value="{{Input::old('product_name')}}">
                                <p class="help-block">产品名称,2-32个字以内</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="status">是否生效</label>
                            <div class="controls">
                                <label class="checkbox">
                                    <input id="status" name="status" {{Input::old('status') != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                                    生效
                                </label>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="is_suggest">是否推荐</label>
                            <div class="controls">
                                <label class="checkbox">
                                    <input id="is_suggest" name="is_suggest" {{Input::old('is_suggest') != 1 ? '' : 'checked="checked"'}} type="checkbox" value="1">
                                    推荐
                                </label>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="sort">排序</label>
                            <div class="controls">
                                <input id="sort" name="sort" class="input-xlarge" type="text" value="{{Input::old('sort',9999)}}">
                                <p class="help-block">数值类型,以降序排列,修改即时生效</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="product_type">所属分类</label>
                            <div class="controls">
                                <select id="product_type" name="product_type">
                                    @foreach( $types as $type )
                                    <option {{$type->id == Input::old('prodcut_type') ? 'selected="selected"' : ''}} value="{{$type->id}}">{{$type->top_name->name.'-'.$type->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">简要描述</label>
                            <div class="controls">
                                <textarea class="input-xlarge" name="info" rows="3" style="width: 500px"></textarea>
                                <p class="help-block">产品简要描述,2-200个字以内</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">详情字段</label>
                            <div class="controls">
                                <table class="table table-bordered table-hover" style="word-break:break-all;">
                                    <thead>
                                    <tr>
                                        <th class="span1">排序</th>
                                        <th class="span2">字段</th>
                                        <th class="span7">内容</th>
                                        <th class="span1">默认</th>
                                        <th class="span1">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody id="attribute_list">

                                    </tbody>
                                </table>
                                <a class="btn btn-inverse" target="_blank" href="{{URL::to('product/attribute_list')}}">字段管理</a>
                                <a class="btn btn-danger" onclick="add_attribute()" id="add_attribute">新增</a>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="image_list">产品图片</label>
                            <div class="controls">
                                <ul id="image_list" class="thumbnails">

                                </ul>
                                <a class="btn btn-inverse" id="add_image_from_path">选择图片</a>
                                <a class="btn btn-danger" id="add_image">上传图片</a>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="video_list">产品视频</label>
                            <div class="controls">
                                <ul class="thumbnails" id="video_list">
                                    <li id="file_list"></li>
                                </ul>
                                <a class="btn btn-danger" id="add_file">添加视频</a>
                                <a onclick="del_file()" class="btn btn-inverse" id="del_file" style="display: none">删除</a>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">标签</label>
                            <div class="controls">
                                @foreach($tags as $tag)
                                <input id="tags" type="checkbox" name="product_tags[]" value="{{$tag->id}}">
                                {{$tag->name}}&nbsp;
                                @endforeach
                                <p class="help-block">用于自动匹配相关产品,建议添加1-3个标签</p>
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
<script>
    var i = 1;
    function add_attribute()
    {
        var wait_dialog = $.dialog({esc:false,title:'加载中...',cancel:false}).lock();
        $.ajax({
            type: "get",
            data:{sort:i},
            url: "/ajax/add_attribute_html",
            success: function(data){
                wait_dialog.close();
                var dialog = $.dialog({
                    content: data,
                    fixed: true,
                    id: 'add_attribute_dialog',
                    lock: true,
                    title: '新增',
                    okVal: '确定',
                    ok: function () {
                        var input = $('#value');
                        var display = $('#display');
                        var sort = $('#attribute_sort').val();
                        if(sort == '')
                            sort = i;

                        if (input.val() == '') {
                            input.select();
                            input.focus();
                            return false;
                        } else {
                            $('#attribute_list').append(
                            '<tr id="attribute_list_id_'+i+'">'
                            + '<td id="sort">'+sort+'</td>'
                            +   '<td true_id="'+$('#attribute_id').find("option:selected").val()+'" id="attribute_id">'+$('#attribute_id').find("option:selected").text()
                            +'<input type="hidden" name="product_attribute[]" value="'+$('#attribute_id').find("option:selected").val()+'||'+input.val().replace(/\"/g,"”")+'||'+sort+'||'+display.find("option:selected").val()+'"></td>'
                            +    '<td id="value">'+input.val()+'</td>'
                            +    '<td id="display" true_display="'+display.find("option:selected").val()+'">'+display.find("option:selected").text()+'</td>'
                            +    '<td class="toolbar">'
                            +    '<div class="btn-group">'
                            +    '<a class="btn btn-flat" onclick="load_attribute('+i+')" data-original-title="修改"><span class="awe-pencil"></span></a>'
                            +'<a class="btn btn-flat" onclick="remove_attribute('+i+')" data-original-title="删除"><span class="awe-remove"></span></a>'
                            +    '</div>'
                            +    '</td>'
                            +'</tr>');
                            i++;
                        };
                    },
                    cancel: true
                });
            },
            error: function(){
                wait_dialog.close();
                $.dialog.alert('系统忙请稍后再试!');
            }
        });
    }

    function remove_attribute(id)
    {
        $('#attribute_list_id_'+id).remove();
    }

    function load_attribute(id)
    {
        var wait_dialog = $.dialog({esc:false,title:'加载中...',cancel:false}).lock();
        $.ajax({
            type: "get",
            data:{value:$('#attribute_list_id_'+id+' #value').text(),attribute_id:$('#attribute_list_id_'+id+' #attribute_id').attr('true_id'),sort:$('#attribute_list_id_'+id+' #sort').text(),display:$('#attribute_list_id_'+id+' #display').attr('true_display')},
            url: "/ajax/add_attribute_html",
            success: function(data){
                wait_dialog.close();
                var dialog = $.dialog({
                    content: data,
                    fixed: true,
                    id: 'edit_attribute_dialog',
                    lock: true,
                    title: '修改',
                    okVal: '确定',
                    ok: function () {
                        var input = $('#value');
                        var display = $('#display');
                        var sort = $('#attribute_sort').val();
                        if(sort == '')
                            sort = id;
                        if (input.val() == '') {
                            input.select();
                            input.focus();
                            return false;
                        } else {
                            $('#attribute_list_id_'+id).html(
                                    '<td id="sort">'+sort+'</td>'
                                    +   '<td true_id="'+$('#attribute_id').find("option:selected").val()+'" id="attribute_id">'+$('#attribute_id').find("option:selected").text()
                                    +'<input type="hidden" name="product_attribute[]" value="'+$('#attribute_id').find("option:selected").val()+'||'+input.val().replace(/\"/g,"”")+'||'+sort+'||'+display.find("option:selected").val()+'"></td>'
                                    +    '<td id="value">'+input.val()+'</td>'
                                    +    '<td id="display" true_display="'+display.find("option:selected").val()+'">'+display.find("option:selected").text()+'</td>'
                                    +    '<td class="toolbar">'
                                    +    '<div class="btn-group">'
                                    +    '<a class="btn btn-flat" onclick="load_attribute('+id+')" data-original-title="修改"><span class="awe-pencil"></span></a>'
                                    +'<a class="btn btn-flat" onclick="remove_attribute('+id+')" data-original-title="删除"><span class="awe-remove"></span></a>'
                                    +    '</div>'
                                    +    '</td>'
                                    );
                        };
                    },
                    cancel: true
                });
            },
            error: function(){
                wait_dialog.close();
                $.dialog.alert('系统忙请稍后再试!');
            }
        });
    }

    function remove_image(id)
    {
        $('#image_id_'+id).remove();
    }

    function del_file()
    {
        $('#file_list').empty();
        $('#del_file').hide();
    }

</script>
@endsection