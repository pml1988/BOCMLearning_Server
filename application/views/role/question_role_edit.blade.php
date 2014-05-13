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
                        <a class="btn btn-inverse" href="{{URL::to('role/question_role_list')}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
                <form name="myform" method="post">
                    <fieldset>
                        <div class="control-group">
                            <div class="controls">
                                <h3>姓名: {{$role->user->user_name}}</h3>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">管理模块</label>
                            <div class="controls row-fluid">
                                @foreach($types as $type)
                                <div class="row span2">
                                    <input onclick="unselectall()" name="question_type_id[]" {{in_array($type->id,explode(',',$role->describe)) ? 'checked="checked"' : ''}} type="checkbox" value="{{$type->id}}">
                                    {{$type->name}}
                                    </div>
                                @endforeach
                                <div class="row span2">
                                    <input name='chkAll' type='checkbox' id='chkAll' onclick='CheckAll(this.form)' value='checkbox'> 全选
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
<script>
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
</script>
@endsection