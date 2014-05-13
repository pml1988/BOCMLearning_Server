@layout('layout.common')

@section('content')
<link href="/css/ui.friendsuggest.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/ui.friendsuggest.js"></script>
<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>{{$web_title}}</h2>
                <ul class="data-header-actions">
                    <li>
                        <a class="btn btn-inverse" href="{{URL::to('role/admin_role_list')}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
                <form method="post" name="myform" id="form">
                    <fieldset>
                        <div class="control-group">
                            <div class="controls">
                                <label class="control-label">选择用户</label>
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
                        </div>
                        <div class="form-actions">
                            <a class="btn btn-large btn-danger" onclick="find_ids()">保存</a>
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
<script type="text/javascript">

    function unselectall(){
        if(document.myform.chkAll.checked){
            document.myform.chkAll.checked = document.myform.chkAll.checked&0;
        }
    }
    function CheckAll(form){
        for (var i=0;i<form.elements.length;i++){
            var e = form.elements[i];
            if (e.Name != 'chkAll'&&e.disabled==false)
                e.checked = form.chkAll.checked;
        }
    }

    function find_ids()
    {
        var arr=new Array();
        $('.ui-fs-result').find("a").each(function(){
            arr.push(this.name)
        })
        $('#ids').attr('value',arr.join(','));
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

    document.getElementsByTagName('form')[0].onkeydown = function(e){
        var e = e || event;
        var keyNum = e.which || e.keyCode;
        return keyNum==13 ? false : true;
    };
</script>
@endsection