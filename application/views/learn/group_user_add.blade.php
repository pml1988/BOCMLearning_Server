@layout('layout.common')

@section('content')
<script src="/js/bootstrap/bootstrap-modal.js"></script>
<link rel="stylesheet" href="/js/plugins/ztree/css/zTreeStyle/zTreeStyle.css" type="text/css">
<script type="text/javascript" src="/js/plugins/ztree/js/jquery.ztree.core-3.5.js"></script>
<script type="text/javascript" src="/js/plugins/ztree/js/jquery.ztree.excheck-3.5.js"></script>

<link href="/css/ui.friendsuggest.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/ui.friendsuggest.js"></script>

<SCRIPT type="text/javascript">
    var setting = {
        async: {
            enable: true,
            url:"/ajax/user_tree",
            autoParam:["id", "level=lv"],
            dataFilter: dataFilter
        },
        check: {
            enable: true,
            autoCheckTrigger: true
        },

        callback: {
            onCheck: onCheck,
            onAsyncSuccess: onAsyncSuccess
        }
    };

    function dataFilter(treeId, parentNode, childNodes) {
//        if (parentNode.checkedEx === true) {
//            for(var i=0, l=childNodes.length; i<l; i++) {
//                childNodes[i].checked = parentNode.checked;
//                childNodes[i].halfCheck = false;
//                childNodes[i].checkedEx = true;
//            }
//        }
        return childNodes;
    }
    function onAsyncSuccess(event, treeId, treeNode, msg) {
        cancelHalf(treeNode);
    }
    function cancelHalf(treeNode) {
        var zTree = $.fn.zTree.getZTreeObj("treeDemo");
        zTree.updateNode(treeNode);
    }
    var code, log, className = "dark";

    function onCheck(e, treeId, treeNode) {
//        showLog("[ onCheck ]&nbsp;&nbsp;&nbsp;&nbsp;" + treeNode.name );
    }
    function showLog(str) {
        if (!log) log = $("#log");
        log.append("<li class='"+className+"'>"+str+"</li>");
        if(log.children("li").length > 6) {
            log.get(0).removeChild(log.children("li")[0]);
        }
    }

    $(document).ready(function(){
        $.fn.zTree.init($("#treeDemo"), setting);
    });
</SCRIPT>

<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>{{$web_title}}</h2>
                <ul class="data-header-actions">
                    <li>
                        <a class="btn btn-inverse" href="{{URL::to('learn/group_user_list?id='.Input::get('group_id'))}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>

                <div class="controls">
                    <label class="control-label">批量添加成员</label>
                    <input type="hidden" id="ids" name="user_id">
                    <div id="ui-fs2" class="ui-fs">
                        <div class="ui-fs-result clearfix">
                        </div>
                        <div class="ui-fs-input">
                            <input type="text" class="input-xlarge" value="输入姓名或工号搜索用户" maxlength="10" />
                        </div>
                        <div class="ui-fs-list">
                            数据加载中....
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a class="btn btn-large btn-danger" onclick="find_ids()">保存</a>
                </div>


            </section>
        </div>
    </article>
    <!-- /Data block -->


</div>
<!-- /Grid row -->
<div class="modal fade hide" id="modal" style="display: none" aria-hidden="false">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>筛选条件/选择部门</h3>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <span>工作时间</span>
            <select id="time_long" style="width: 100px">
                <option value="1">不限</option>
                <option value="2">5年以下</option>
                <option value="3">5-10年</option>
                <option value="4">10年以上</option>
            </select>
            <div class="zTreeDemoBackground">
                <ul id="treeDemo" class="ztree"></ul>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-alt" data-dismiss="modal">取消</a>
        <a href="javascript:void(0)" onclick="select()" class="btn btn-alt btn-primary">确定</a>
    </div>
</div>

<form method="post" id="form" style="border: none">
<!-- Grid row -->
<div class="row">

<!-- Data block -->
<article class="span12 data-block">
<div class="data-container">
<header>
    <h2>通过筛选添加成员</h2>
    <ul class="data-header-actions">
        <li>
            <a class="btn btn-danger" data-toggle="modal" href="#modal">筛选条件/选择部门</a><br>
        </li>
    </ul>
</header>
<section>

    <div class="row-fluid" id="data_table">

<table class="datatable table table-striped table-bordered table-hover" id="table">
    <thead>
    <tr>
        <th>工号</th>
        <th>姓名</th>
        <th>银行/部门</th>
        <th>入行时间</th>
        <th>选择</th>
    </tr>
    </thead>
    <tbody></tbody>
</table>

</div>
<div class="form-actions selected">
    <button class="btn btn-large btn-danger" type="submit">保存</button>
</div>

</section>
</div>
</article>
<!-- /Data block -->


</div>
<!-- /Grid row -->
</form>
@endsection

@section('scripts')
<script src="/js/plugins/dataTables/jquery.datatables.min.js"></script>
<script>
    function select()
    {
        var bank_id = new Array();
        $('#modal').modal('hide');
        var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
        var nodes = treeObj.getCheckedNodes(true);
        for(var i=0;i<nodes.length;i++){
            bank_id[i] = nodes[i].id;
        }
        load_data_table(bank_id.join(','));
    }

    $.extend( $.fn.dataTableExt.oStdClasses, {
        "sWrapper": "dataTables_wrapper form-inline"
    } );

    $.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
    {
        return {
            "iStart":         oSettings._iDisplayStart,
            "iEnd":           oSettings.fnDisplayEnd(),
            "iLength":        oSettings._iDisplayLength,
            "iTotal":         oSettings.fnRecordsTotal(),
            "iFilteredTotal": oSettings.fnRecordsDisplay(),
            "iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
            "iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
        };
    }

    /* Bootstrap style pagination control */
    $.extend( $.fn.dataTableExt.oPagination, {
        "bootstrap": {
            "fnInit": function( oSettings, nPaging, fnDraw ) {
                var oLang = oSettings.oLanguage.oPaginate;
                var fnClickHandler = function ( e ) {
                    e.preventDefault();
                    if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
                        fnDraw( oSettings );
                    }
                };

                $(nPaging).addClass('pagination').append(
                    '<ul>'+
                        '<li class="prev disabled"><a href="#">&larr; 上一页</a></li>'+
                        '<li class="next disabled"><a href="#">下一页 &rarr; </a></li>'+
                        '</ul>'
                );
                var els = $('a', nPaging);
                $(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
                $(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
            },

            "fnUpdate": function ( oSettings, fnDraw ) {
                var iListLength = 5;
                var oPaging = oSettings.oInstance.fnPagingInfo();
                var an = oSettings.aanFeatures.p;
                var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

                if ( oPaging.iTotalPages < iListLength) {
                    iStart = 1;
                    iEnd = oPaging.iTotalPages;
                }
                else if ( oPaging.iPage <= iHalf ) {
                    iStart = 1;
                    iEnd = iListLength;
                } else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
                    iStart = oPaging.iTotalPages - iListLength + 1;
                    iEnd = oPaging.iTotalPages;
                } else {
                    iStart = oPaging.iPage - iHalf + 1;
                    iEnd = iStart + iListLength - 1;
                }

                for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
                    // Remove the middle elements
                    $('li:gt(0)', an[i]).filter(':not(:last)').remove();

                    // Add the new list items and their event handlers
                    for ( j=iStart ; j<=iEnd ; j++ ) {
                        sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
                        $('<li '+sClass+'><a href="#">'+j+'</a></li>')
                            .insertBefore( $('li:last', an[i])[0] )
                            .bind('click', function (e) {
                                e.preventDefault();
                                oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
                                fnDraw( oSettings );
                            } );
                    }

                    // Add / remove disabled classes from the static elements
                    if ( oPaging.iPage === 0 ) {
                        $('li:first', an[i]).addClass('disabled');
                    } else {
                        $('li:first', an[i]).removeClass('disabled');
                    }

                    if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
                        $('li:last', an[i]).addClass('disabled');
                    } else {
                        $('li:last', an[i]).removeClass('disabled');
                    }
                }
            }
        }
    });

    function load_data_table(bank_id) {

        $("#data_table").find('table').dataTable( {
            "bDestroy": true,
//            "bRetrieve": true,
            "bLengthChange": false,
            "bFilter": false,
            "bProcessing": true,
            "bSort" : false,
            "sAjaxSource": "/ajax/group_user?bank_id="+bank_id+"&time_long="+$('#time_long').find("option:selected").val(),
            "sPaginationType": "bootstrap",
            "oLanguage": {
                "sLengthMenu": "_MENU_ 每页条数",
                "sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
                "sZeroRecords": "暂无数据",
                "sInfoEmpty": "没有数据",
                "sZeroRecords": "没有检索到数据",
                "sProcessing": "<span class=\"loading red\" data-original-title=\"处理中...\"></span>加载中..."
            }

        });
        $('.datatable-controls').on('click','li input',function(){
            dtShowHideCol( $(this).val() );
        })
    }

    function select_user(obj)
    {
        var user_id = $(obj).attr('value');

        if( $(obj).is(':checked') )
        {
            var spanDom = '<input type="hidden" name="user_id[]" id="s'+user_id+'" value="'+user_id+'">';
            $('.selected').append(spanDom);
        }
        else
        {
            $('#s'+user_id).remove();
        }

    }

    function find_ids()
    {
        $('.ui-fs-result').find("a").each(function(){
            var spanDom = '<input type="hidden" name="user_id[]" id="s'+this.name+'" value="'+this.name+'">';
            $('.selected').append(spanDom);
        })
        document.getElementById("form").submit();
    }

    $(document).ready(function(){
        //单选模式
        var test2 = new giant.ui.friendsuggest({
            btnAll:"#ui-fs2 .ui-fs-icon",
            btnCloseAllFriend:"#ui-fs2 .ui-fs-all .close",
            btnNextPage:"#ui-fs2 .ui-fs-all .next",
            btnPrevPage:"#ui-fs2 .ui-fs-all .prev",
            allFriendContainer:"#ui-fs2 .ui-fs-all" ,
            allFriendListContainer:"#ui-fs2 .ui-fs-all .ui-fs-allinner div.list",
            frinedNumberContainer:"#ui-fs2 .ui-fs-allinner .page b",
            totalSelectNum:1,
            resultContainer:"#ui-fs2 .ui-fs-result",
            input:"#ui-fs2 .ui-fs-input input",
            inputContainer:"#ui-fs2 .ui-fs-input",
            dropDownListContainer:"#ui-fs2 .ui-fs-list",
            selectType:"multiple"
        });
    });
</script>

@endsection