<?php
/* @var $categories
 * @var $bar
 * @var $orders
 * @var $registers
 * @var $newMessagesCount
 * @var $equipments Equipment[]
 * @var $equipmentsCount
 * @var $messagesChat
 * @var $usersCount
 * @var $currentUser
 * @var $objectsCount
 * @var $objectsTypeCount
 * @var $services
 * @var $channelsCount
 * @var $eventsCount
 * @var $layer
 * @var $values
 */
$this->title = Yii::t('app', 'ПолиТЭР::Сводная');
$this->registerJsFile('/js/vendor/lib/HighCharts/highcharts.js');
$this->registerJsFile('/js/vendor/lib/HighCharts/modules/exporting.js');

use common\models\Equipment;

?>

<!-- Info boxes -->
<div class="row">
    <?= $this->render('widget-full-stats', ['objectsCount' => $objectsCount,
        'channelsCount' => $channelsCount, 'eventsCount' => $eventsCount]); ?>
</div>
<!-- /.row -->

<?php echo common\widgets\Alert::widget() ?>

<div class="row">
    <div class="col-md-7">
        <?= $this->render('widget-events-stats',
            ['categories' => $categories,
                'values' => $values]); ?>
    </div>
    <!-- /.col -->
    <div class="col-md-5">
        <?= $this->render('widget-equipments', ['equipments' => $equipments]); ?>
    </div>
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-8">
        <?= $this->render('widget-map', ['layer' => $layer]); ?>
        <div class="row">
            <div class="col-md-7">
                <?php
                echo $this->render('widget-register', ['registers' => $registers]);
                ?>
            </div>
        </div>
        <?= $this->render('widget-orders', ['orders' => $orders]); ?>
    </div>

    <div class="col-md-4">
        <?= $this->render('widget-objects', ['objects' => $objects]); ?>
    </div>
</div>

<footer class="main-footer" style="margin-left: 0 !important;">
    <?= $this->render('footer'); ?>
</footer>
