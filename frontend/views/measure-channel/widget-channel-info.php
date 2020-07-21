<?php
/* @var $channel MeasureChannel
 */

use common\models\MeasureChannel; ?>

<div class="col-md-6 col-sm-6 col-xs-6">
    <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-info"></i></span>
        <div class="info-box-content">
            <?php echo '[#' . $channel->_id . '] ' . $channel->title . '<br/>'; ?>
            <?php echo $channel->object->getFullTitle() . '<br/>'; ?>
            <?php echo $channel->measureType->title . ' [' . $channel->getTypeName() . ']<br/>'; ?>
        </div>
    </div>
</div>

<div class="col-md-6 col-sm-6 col-xs-6">
    <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-cogs"></i></span>
        <div class="info-box-content">
            <?php echo 'Номер параметра: ' . $channel->param_id . '<br/>'; ?>
            <?php echo $channel->original_name . '<br/>'; ?>
            <?php echo $channel->path . '<br/>'; ?>
        </div>
    </div>
</div>
