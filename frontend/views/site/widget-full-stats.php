<?php
/* @var $objectsCount
 * @var $objectsTypeCount
 * @var $objectsSubTypeCount
 * @var $channelsCount
 * @var $eventsCount
 * @var $eventTypesCount
 * @var $parametersCount
 * @var $parameterTypesCount
 * @var $measuresCount
 * @var $measureTypesCount
 */
?>

<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/events"><span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span></a>
        <div class="info-box-content">
            <a href="/event"><span class="info-box-text">
                    <?php echo Yii::t('app', 'События') ?></span></a>
            <span><a href="/event"><?php echo Yii::t('app', 'Типов событий') . ' ' . $eventTypesCount; ?></a><br/>
            <span class="info-box-number"><?= $eventsCount ?></span>
        </div>
    </div>
</div>
<!-- /.col -->
<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/channels"><span class="info-box-icon bg-red"><i class="fa fa-plug"></i></span></a>

        <div class="info-box-content">
            <a href="/channels"><span class="info-box-text"><?php echo Yii::t('app', 'Измерения') ?></span></a>
            <span><a href="/channels"><?php echo Yii::t('app', 'Каналов измерения') . ' ' . $channelsCount; ?></a> /
                    <a href="/measures"><?php echo Yii::t('app', 'Типов измерений') . ' ' . $measureTypesCount; ?></a></span><br/>
            <span class="info-box-number"><?= $channelsCount ?></span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->

<!-- fix for small devices only -->
<div class="clearfix visible-sm-block"></div>

<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/objects/tree"><span class="info-box-icon bg-green"><i class="fa fa-map-marker"></i></span></a>

        <div class="info-box-content">
            <span class="info-box-text"><?php echo Yii::t('app', 'Объекты') ?></span>
            <span><a href="/object-type"><?php echo Yii::t('app', 'Типов объектов') . ' ' . $objectsTypeCount; ?></a><br/>
                <span><a href="/object-type"><?php echo Yii::t('app', 'Подтипов') . ' ' . $objectsSubTypeCount; ?></a><br/>
                <span class="info-box-number"><?= $objectsCount ?></span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->
<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/parameter"><span class="info-box-icon bg-yellow"><i class="fa fa-info"></i></span></a>

        <div class="info-box-content">
            <span class="info-box-text"><?php echo Yii::t('app', 'Параметры') ?></span>
            <span><a href="/object-type"><?php echo Yii::t('app', 'Типов параметров') . ' ' . $parameterTypesCount; ?></a><br/>
                <span class="info-box-number"><?= $parametersCount ?></span>
        </div>
    </div>
    <!-- /.info-box -->
</div>
