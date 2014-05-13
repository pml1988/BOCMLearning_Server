@layout('layout.common')

@section('content')

<!-- Grid row -->
<div class="row">

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>{{$web_title}}</h2>
            </header>
            <section>

                <div class="row-fluid">
                    <form method="get" style="margin: 0;background: none;border: none">

                        <div class="span5">
                            <span>操作时间</span>
                            <input class="span4 Wdate" id="d1" onclick="WdatePicker({maxDate:'#F{$dp.$D(\'d2\')}'})" type="text" name="search_start" value="{{Input::get('search_start')}}">
                            至
                            <input class="span4 Wdate" id="d2" onclick="WdatePicker({minDate:'#F{$dp.$D(\'d1\')}'})" type="text" name="search_end" value="{{Input::get('search_end')}}">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-inverse pull-right">检索</button>
                        </div>
                    </form>
                </div>

                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th>操作者</th>
                        <th>操作内容</th>
                        <th>操作时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->id}}</td>
                        <td>{{@$row->user->user_name}}</td>
                        <td>{{$row->message}}</td>
                        <td>{{$row->created_at}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$page_link->links()}}
            </section>
        </div>
    </article>
    <!-- /Data block -->


</div>
<!-- /Grid row -->
@endsection

@section('scripts')
<script src="/js/plugins/My97DatePicker/WdatePicker.js"></script>
@endsection