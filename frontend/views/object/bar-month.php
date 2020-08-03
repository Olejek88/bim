<?php
/* @var $measures
 * @var $categories
 * @var $title
 * @var $values
 * @var $measureChannelHeat
 * @var $measureChannelWater
 * @var $measureChannelEnergy
 */
$this->title = Yii::t('app', 'ПолиТЭР::Архив потребления по месяцам');
?>

<div class="row">
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-12">
                <?php
                if ($measureChannelHeat)
                    echo $this->render('widget-month-bar',
                        ['categories' => $categories['heat'], 'title' => 'Тепло', 'id' => 1, 'values' => $values['heat']]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                if ($measureChannelWater)
                    $this->render('widget-month-bar',
                        ['categories' => $categories['water'], 'title' => 'Вода', 'id' => 2, 'values' => $values['water']]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                if ($measureChannelEnergy)
                    $this->render('widget-month-bar',
                        ['categories' => $categories['energy'], 'title' => 'Электроэнергия', 'id' => 3, 'values' => $values['energy']]);
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <?= $this->render('widget-month-data', ['measures' => $measures]); ?>
    </div>
</div>
