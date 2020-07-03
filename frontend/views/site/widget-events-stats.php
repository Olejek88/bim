<?php

use yii\helpers\Html;

/* @var $sumOrderStatusCount
 * @var $sumOrderStatusCompleteCount
 * @var $sumTaskStatusCount
 * @var $sumTaskStatusCompleteCount
 * @var $sumStageStatusCount
 * @var $sumStageStatusCompleteCount
 * @var $sumOperationStatusCount
 * @var $sumOperationStatusCompleteCount
 * @var $ordersStatusCount
 * @var $ordersStatusPercent
 * @var $sumOrderStatusCount
 * @var $categories
 * @var $values
 */

$this->registerJsFile('/js/vendor/lib/HighCharts/highcharts.js');
$this->registerJsFile('/js/vendor/lib/HighCharts/modules/exporting.js');
?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'Статистика событий') ?></h3>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-wrench"></i></button>
                <ul class="dropdown-menu" role="menu">
                    <li><?php echo Html::a(Yii::t('app', 'Календарь нарядов'), ['/orders/calendar']); ?></li>
                    <li><?php echo Html::a(Yii::t('app', 'Наряды'), ['/orders']); ?></li>
                    <li class="divider"></li>
                    <li><?php echo Html::a(Yii::t('app', 'Анализ нарядов'), ['/analytics/orders-stats']); ?></li>
                </ul>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">
                    <strong><?php echo Yii::t('app', 'Расклад нарядов по месяцам за текущий год') ?></strong>
                </p>
                <div class="chart">
                    <div id="container" style="height: 250px;"></div>
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
                                        text: '<?php echo Yii::t('app', 'Количество нарядов по месяцам')?>'
                                    }
                                },
                                series: [<?php echo $values; ?>]
                            });
                        });
                    </script>
                </div>
                <!-- /.chart-responsive -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>

    <!-- ./box-body -->
    <div class="box-footer">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <p class="text-center">
                    <strong><?php echo Yii::t('app', 'Расклад по статусам') ?></strong>
                </p>
                <div class="progress-group">
                    <span class="progress-text"><?php echo Yii::t('app', 'Новых') ?></span>
                    <span
                            class="progress-number"><b><?= $ordersStatusCount[0]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                    <div class="progress sm">
                        <div class="progress-bar progress-bar-aqua"
                             style="width: <?= number_format($ordersStatusPercent[0], 0); ?>%"></div>
                    </div>
                </div>
                <!-- /.progress-group -->
                <div class="progress-group">
                    <span class="progress-text"><?php echo Yii::t('app', 'Не выполнено и отменено') ?></span>
                    <span
                            class="progress-number"><b><?= $ordersStatusCount[1]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                    <div class="progress sm">
                        <div class="progress-bar progress-bar-red"
                             style="width: <?= number_format($ordersStatusPercent[1], 0); ?>%"></div>
                    </div>
                </div>
                <!-- /.progress-group -->
                <div class="progress-group">
                    <span class="progress-text"><?php echo Yii::t('app', 'Всего выполнено') ?></span>
                    <span
                            class="progress-number"><b><?= $ordersStatusCount[2]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                    <div class="progress sm">
                        <div class="progress-bar progress-bar-green"
                             style="width: <?= number_format($ordersStatusPercent[2], 0); ?>%"></div>
                    </div>
                </div>
                <!-- /.progress-group -->
                <div class="progress-group">
                    <span class="progress-text"><?php echo Yii::t('app', 'В работе') ?></span>
                    <span
                            class="progress-number"><b><?= $ordersStatusCount[3]; ?></b>/<?= $sumOrderStatusCount; ?></span>

                    <div class="progress sm">
                        <div class="progress-bar progress-bar-yellow"
                             style="width: <?= number_format($ordersStatusPercent[3], 0); ?>%"></div>
                    </div>
                </div>
                <!-- /.progress-group -->
            </div>
            <!-- /.col -->
            <!--            <div class="col-sm-3 col-xs-6">
                <div class="description-block border-right">
                    <span class="description-percentage text-green"><i
                                class="fa fa-caret-up"></i> <?php /*if ($sumOrderStatusCount > 0) echo number_format($sumOrderStatusCompleteCount * 100 / $sumOrderStatusCount, 2) . '%' */ ?> </span>
                    <h5 class="description-header"><? /*= $sumOrderStatusCount */ ?>
                        / <? /*= $sumOrderStatusCompleteCount */ ?></h5>
                    <span class="description-text">Всего нарядов / Выполнено</span>
                </div>
                <div class="description-block border-right">
                    <span class="description-percentage text-yellow"><i
                                class="fa fa-caret-left"></i> <?php /*if ($sumTaskStatusCount > 0) echo number_format($sumTaskStatusCompleteCount * 100 / $sumTaskStatusCount, 2) . '%' */ ?></span>
                    <h5 class="description-header"><? /*= $sumTaskStatusCount */ ?> / <? /*= $sumTaskStatusCompleteCount */ ?></h5>
                    <span class="description-text">Задач / Выполнено</span>
                </div>
            </div>
-->        </div>
        <!-- /.row -->
    </div>
    <!-- /.box-footer -->
</div>
