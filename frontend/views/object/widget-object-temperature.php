<?php

/* @var $categories
 * @var $values
 */

$this->registerJsFile('/js/HighCharts/highcharts.js');
$this->registerJsFile('/js/HighCharts/modules/exporting.js');
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'Температурный анализ') ?></h3>
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
                <p class="text-center">
                    <strong><?php echo Yii::t('app', 'Расклад потребления по месяцам за три года') ?></strong>
                </p>
                <div class="chart">
                    <div id="container" style="height: 350px;"></div>
                    <script type="text/javascript">
                        document.addEventListener("DOMContentLoaded", function () {
                            Highcharts.chart('container', {
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
                                    '#dd4b39',
                                    '#00c0ef',
                                    '#cd4b39',
                                    '#10c0ef',
                                    '#bd4b39',
                                    '#20c0ef'
                                ],
                                plotOptions: {
                                    column: {
                                        dataLabels: {
                                            enabled: true,
                                            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                                        }
                                    }
                                },
                                yAxis: {
                                    title: {
                                        text: '<?php echo Yii::t('app', 'Распределение по месяцам')?>'
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
