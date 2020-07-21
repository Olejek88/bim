<?php
/* @var $object Objects
 */

use common\models\Objects;

?>

<div class="col-md-6 col-sm-6 col-xs-6">
    <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-info"></i></span>
        <div class="info-box-content">
            <?php echo '[#' . $object->_id . '] ' . $object->getFullTitle() . '<br/>'; ?>
            <?php echo $object->getParentsTitle() . '<br/>'; ?>
            <?php echo 'Подтип ' . $object->objectSubType->title . '<br/>'; ?>
        </div>
    </div>
</div>

<div class="col-md-6 col-sm-6 col-xs-6">
    <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-cogs"></i></span>
        <div class="info-box-content">
            <?php echo 'ФИАС: ' . $object->fiasGuid . '<br/>'; ?>
            <?php echo 'ФИАС родительский: ' . $object->fiasParentGuid . '<br/>'; ?>
            <?php echo 'ОКАТО: ' . $object->okato . '<br/>'; ?>
        </div>
    </div>
</div>
