<?php

/* @var $leafLet */

use dosamigos\leaflet\widgets\Map;

$this->title = Yii::t('app', 'ТОИРУС::Карта');
$this->registerJs('$(window).on("resize", function () { $("#w0").height($(window).height()-50); $("#w0").width($(window).width()-50); }).trigger("resize");');
?>

<div class="box-relative" style="width: 100%">
    <?php
    echo Map::widget(['leafLet' => $leafLet]);
    ?>
</div>
