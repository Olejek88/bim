<?php
/* @var $categories
 * @var $bar
 * @var $channels
 * @var $registers
 * @var $events
 * @var $objectsCount
 * @var $objectsTypeCount
 * @var $channelsCount
 * @var $measuresCount
 * @var $measureTypesCount
 * @var $parametersCount
 * @var $parameterTypesCount
 * @var $objectsSubTypeCount
 * @var $eventsCount
 * @var $eventTypesCount
 * @var $layer
 * @var $values
 */
$this->title = Yii::t('app', 'ПолиТЭР::Сводная');
$this->registerJsFile('/js/HighCharts/highcharts.js');
$this->registerJsFile('/js/HighCharts/modules/exporting.js');

?>

<!-- Info boxes -->
<div class="row">
    <?= $this->render('widget-full-stats', ['objectsCount' => $objectsCount, 'objectsTypeCount' => $objectsTypeCount,
        'objectsSubTypeCount' => $objectsSubTypeCount,
        'measureTypesCount' => $measureTypesCount, 'parametersCount' => $parametersCount, 'parameterTypesCount' => $parameterTypesCount,
        'channelsCount' => $channelsCount, 'eventsCount' => $eventsCount, 'eventTypesCount' => $eventTypesCount, 'measuresCount' => $measuresCount]); ?>
</div>
<!-- /.row -->

<?php echo common\widgets\Alert::widget() ?>

<div class="row">
    <div class="col-md-7">
        <?= /*$this->render('widget-events-stats',
            ['categories' => $categories,
                'values' => $values]);*/
        "" ?>
    </div>
    <!-- /.col -->
    <div class="col-md-5">
        <?= /*$this->render('widget-equipments', ['equipments' => $equipments]);*/
        "" ?>
    </div>
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-8">
        <?= $this->render('widget-map', ['layer' => $layer]); ?>
        <div class="row">
            <div class="col-md-12">
                <?php
                echo $this->render('widget-events', ['events' => $events]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->render('widget-last-channels', ['channels' => $channels]); ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <?php echo $this->render('widget-last-channels', ['channels' => $channels]); ?>
    </div>
</div>

<footer class="main-footer" style="margin-left: 0 !important;">
    <?= $this->render('footer'); ?>
</footer>
