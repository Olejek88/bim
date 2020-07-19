<?php

/* @var $title
 * @var $categories
 * @var $values
 * @var $id
 */

$this->registerJsFile('/js/vendor/lib/HighCharts/highcharts.js');
$this->registerJsFile('/js/vendor/lib/HighCharts/modules/exporting.js');
?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo $title ?></h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="chart">
                    <div id="container<?= $id ?>" style="height: 250px;"></div>
                    <script type="text/javascript">
                        document.addEventListener("DOMContentLoaded", function () {
                            Highcharts.chart('container<?= $id?>', {
                                data: {
                                    table: 'datatable'
                                },
                                chart: {
                                    type: 'column'
                                },
                                title: {
                                    text: ''
                                },
                                xAxis: {
                                    categories: [<?php echo $categories; ?>]
                                },
                                legend: {
                                    align: 'right',
                                    x: 0,
                                    verticalAlign: 'top',
                                    y: 0,
                                    floating: true,
                                    backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                                    borderColor: '#CCC',
                                    borderWidth: 1,
                                    shadow: false
                                },
                                tooltip: {
                                    headerFormat: '<b>{point.x}</b><br/>',
                                    pointFormat: '{series.name}: {point.y}<br/>' + '<?php echo Yii::t('app', 'Всего')?>' + ' : {point.stackTotal}'
                                },
                                colors: [
                                    '#00c0ef',
                                    '#dd4b39',
                                    '#00a65a',
                                    '#f39c12'
                                ],
                                plotOptions: {
                                    column: {
                                        stacking: 'normal',
                                        dataLabels: {
                                            enabled: true,
                                            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                                        }
                                    }
                                },
                                yAxis: {
                                    min: 0,
                                    title: {
                                        text: '<?php echo $title ?>'
                                    }
                                },
                                series: [<?php echo $values; ?>]
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
