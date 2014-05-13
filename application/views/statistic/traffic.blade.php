@layout('layout.common')

@section('content')
<script type="text/javascript">
    $(function () {
        var chart;
        $(document).ready(function() {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container',
                    zoomType: 'xy'
                },
                title: {
                    text: '手机客户端月流量统计'
                },
                subtitle: {
                    text: ''
                },
                xAxis: [{
                    categories: [<?php echo $chart['date']?>]
                }],
                yAxis: [{ // Primary yAxis
                    labels: {
                        formatter: function() {
                            return this.value +' 人';
                        },
                        style: {
                            color: '#89A54E'
                        }
                    },
                    title: {
                        text: '日活跃用户数',
                        style: {
                            color: '#89A54E'
                        }
                    },
                    allowDecimals:false
                }, { // Secondary yAxis
                    title: {
                        text: '日总请求数',
                        style: {
                            color: '#4572A7'
                        }
                    },
                    labels: {
                        formatter: function() {
                            return this.value +' 次';
                        },
                        style: {
                            color: '#4572A7'
                        }
                    },
                    opposite: true,
                    allowDecimals:false
                }],
                tooltip: {
                    formatter: function() {
                        return ''+
                            '<?php echo Input::get('date',date('Y-m')).'-' ?>'+this.x +': '+ this.y +
                            (this.series.name == '日总请求数' ? ' 次' : ' 人');
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    x: 120,
                    verticalAlign: 'top',
                    y: 0,
                    floating: true,
                    backgroundColor: '#FFFFFF'
                },
                series: [{
                    name: '日总请求数',
                    color: '#4572A7',
                    type: 'column',
                    yAxis: 1,
                    data: [<?php echo $chart['count_request']?>]

                }, {
                    name: '日活跃用户数',
                    color: '#89A54E',
                    type: 'spline',
                    data: [<?php echo $chart['count_user']?>]
                }],
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
                }
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
<!--                        <a class="btn btn-inverse" href="#">立即刷新</a>-->
                    </li>
                </ul>
            </header>
            <section>
                <div class="row-fluid">
                    <form method="get" style="margin: 0;background: none;border: none">

                        <div class="span5">
                            <span>选择月份</span>
                            <input class="span4 Wdate" id="d1" onclick="WdatePicker({dateFmt:'yyyy-MM'})" type="text" name="date" value="{{Input::get('date',date('Y-m'))}}">
                            <button type="submit" class="btn btn-inverse">查看</button>
                        </div>
                    </form>
                </div>
                <div id="container" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
            </section>
        </div>
    </article>
    <!-- /Data block -->

    <!-- Data block -->
    <article class="span12 data-block">
        <div class="data-container">
            <header>
                <h2>平台总访问量共<strong>{{$total}}</strong>次</h2>
            </header>

        </div>
    </article>
    <!-- /Data block -->


</div>
<!-- /Grid row -->
@endsection

@section('scripts')
<script src="/js/plugins/highchart/highcharts.js"></script>
<script src="/js/plugins/highchart/modules/exporting.js"></script>
<script src="/js/plugins/My97DatePicker/WdatePicker.js"></script>
@endsection