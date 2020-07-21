<?php
/* @var $categories
 * @var $bar
 * @var $channel
 * @var $measures
 */
$this->title = Yii::t('app', 'ПолиТЭР::Канал измерения');
$this->registerJsFile('/js/HighCharts/highcharts.js');
$this->registerJsFile('/js/HighCharts/modules/exporting.js');

?>

<div class="row">
    <?= $this->render('widget-channel-info', ['channel' => $channel]); ?>
</div>

<div class="row">
    <div class="col-md-9">
        <?= $this->render('widget-channel-bar',
            ['categories' => $categories, 'title' => $channel->title, 'id' => 1, 'values' => $values]);
        ?>
    </div>
    <div class="col-md-3">
        <?= $this->render('widget-channel-data', ['measures' => $measures]); ?>
    </div>
</div>
