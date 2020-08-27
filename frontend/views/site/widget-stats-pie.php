<?php
/* @var $data
 * @var $title
 */

$this->registerJsFile('/jsHighCharts/highcharts.js');
$this->registerJsFile('/jsHighCharts/modules/exporting.js');
?>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo $title ?></h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i></button>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="chart-responsive">
                    <div id="pie<?= $id ?>" style="min-width:400px; width:100%; height:90%">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.footer -->
</div>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        Highcharts.chart('pie<?= $id?>', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: '<?php echo $title ?>'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            legend: {
                align: 'right',
                layout: 'vertical'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    },
                    showInLegend: true
                }
            },
            series: [{
                <?php
                $first = 0;
                $bar = "name: '" . Yii::t('app', 'Типы') . "',";
                $bar .= "colorByPoint: true,";
                $bar .= "data: [";
                foreach ($data as $d) {
                    if ($first > 0)
                        $bar .= "," . PHP_EOL;
                    $bar .= '{';
                    $bar .= 'name: \'' . $d['title'] . '\',';
                    $bar .= 'y: ' . $d['cnt'];
                    if ($first == 0)
                        $bar .= ",sliced: true, selected: true" . PHP_EOL;
                    $bar .= '}';
                    $first++;
                }
                $bar .= "]}]";
                echo $bar;
                ?>
            });
    });
</script>
