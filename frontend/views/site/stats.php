<?php
/* @var $categories_month
 * @var $values_month
 * @var $categories_days
 * @var $values_days
 * @var $channels
 * @var $data_by_source
 * @var $data_by_type
 * @var $stats
 */

$this->title = Yii::t('app', 'ПолиТЭР::Статистический анализ');
$this->registerJsFile('/js/HighCharts/highcharts.js');
$this->registerJsFile('/js/HighCharts/modules/exporting.js');

?>

<!-- Info boxes -->
<div class="row">
    <div class="col-md-7">
        <div class="row">
            <div class="col-md-12">
                <?= $this->render('widget-stats-bar', ['id' => '1', 'categories' => $categories_month, 'values' => $values_month, 'title' => 'Количество данных по месяцам']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $this->render('widget-stats-bar', ['id' => '2', 'categories' => $categories_days, 'values' => $values_days, 'title' => 'Количество данных по дням']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $this->render('widget-stats-pie', ['id' => '1', 'data' => $data_by_source, 'title' => 'По ресурсам']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $this->render('widget-stats-pie', ['id' => '2', 'data' => $data_by_type, 'title' => 'По типам']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $this->render('widget-stats-main', ['stats' => $stats]); ?>
            </div>
        </div>
    </div>
    <!-- /.col -->
    <div class="col-md-5">
        <?= $this->render('widget-stats-channels', ['channels' => $channels]); ?>
    </div>
</div>

<footer class="main-footer" style="margin-left: 0 !important;">
    <?= $this->render('footer'); ?>
</footer>
