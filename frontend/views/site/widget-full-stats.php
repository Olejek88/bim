<?php
/* @var $objectsCount
 * @var $objectsTypeCount
 * @var $channelsCount
 * @var $eventsCount
 * @var $measuresCount
 */
?>

<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/events"><span class="info-box-icon bg-aqua"><i class="fa fa-calendar"></i></span></a>
        <div class="info-box-content">
            <a href="/event"><span class="info-box-text">
                    <?php echo Yii::t('app', 'События') ?></span></a>
        </div>
    </div>
</div>
<!-- /.col -->
<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
        <a href="/channels"><span class="info-box-icon bg-red"><i class="fa fa-plug"></i></span></a>

        <div class="info-box-content">
            <a href="/channels"><span class="info-box-text"><?php echo Yii::t('app', 'Каналы измерения') ?></span></a>
            <span><a href="/channels"><?php echo Yii::t('app', 'Каналов измерения') . ' ' . $channelsCount; ?></a> /
                    <a href="/measures"><?php echo Yii::t('app', 'Измерений') . ' ' . $measuresCount; ?></a></span><br/>
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
            <span><a href="/object-type"><?php echo Yii::t('app', 'Типов') . ' ' . $objectsTypeCount; ?></a><br/>
                <span class="info-box-number"><?= $objectsCount ?></span>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->
<div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box">
    </div>
    <!-- /.info-box -->
</div>
