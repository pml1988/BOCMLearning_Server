@layout('layout.common')

@section('content')
<script>
    $(function () {
        var chart;
        $(document).ready(function() {

            // Radialize the colors
            Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
                return {
                    radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
                    stops: [
                        [0, color],
                        [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
                    ]
                };
            });

            // Build the chart
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                lang: {
                    decimalPoint: ".",
                    downloadPNG: "保存为PNG",
                    downloadJPEG: "保存为JPEG",
                    downloadPDF: "保存为PDF",
                    downloadSVG: "保存为SVG",
                    exportButtonTitle: "Export to raster or vector image"
                },
                credits: {
                    enabled: true,
                    href:'#',
                    text: "江苏中行-产品知识管理平台"
                },
                title: {
                    text: '学习任务完成情况统计'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y}人</b>',
                    percentageDecimals: 1
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function() {
                                return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage,2,'.') +' %';
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: '人数',
                    data: [
                        ['已完成',  {{$complete_count}}],
                        ['未完成',  {{$total - $complete_count}}]
                    ]
                }]
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
                        <a class="btn btn-inverse" href="{{URL::to(base64_decode(Input::get('ref')))}}">返回</a>
                    </li>
                </ul>
            </header>
            <section>
                <div id="container" style="min-width: 300px; height: 300px; margin: 0 auto"></div>

                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>工号</th>
                        <th>姓名</th>
                        <th>任务完成状态</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $list as $row )
                    <tr>
                        <td>{{$row->user->job_code}}</td>
                        <td>{{$row->user->user_name}}</td>
                        <?php $status = UserTask::where('user_id','=',$row->user_id)->where('task_id','=',$task->id)->first()->complete_status == 1
                            ? true : false;?>
                        <td><span class="label {{$status == true ? 'label-important' : ''}}">
                                {{$status == true ? '已完成' : '未完成'}}
                            </span></td>
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
<script src="/js/plugins/highchart/highcharts.js"></script>
<script src="/js/plugins/highchart/modules/exporting.js"></script>
@endsection