<?php
/* @var $object
 * @var $marker
 * @var $coordinates
 * @var $parameters
 * @var $events
 * @var $channels
 * @var $alarms
 * @var $measures
 * @var $registers
 */
$this->title = Yii::t('app', 'ПолиТЭР::Информация об объекте');
?>

<div class="row">
    <?= $this->render('widget-object-info', ['object' => $object]); ?>
</div>

<div class="row">
    <div class="col-md-5">
        <?= $this->render('widget-object-map', ['object' => $object, 'marker' => $marker, 'coordinates' => $coordinates]); ?>
    </div>
    <div class="col-md-7">
        <?= $this->render('widget-object-parameters', ['parameters' => $parameters]); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <?= $this->render('widget-object-channels', ['channels' => $channels]); ?>
    </div>
    <div class="col-md-4">
        <?= $this->render('widget-object-events', ['events' => $events]); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $this->render('widget-object-alarms', ['alarms' => $alarms]); ?>
    </div>
    <div class="col-md-6">
        <?= $this->render('widget-object-consumption', ['measures' => $measures]); ?>
    </div>
</div>
