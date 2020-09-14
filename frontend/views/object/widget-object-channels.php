<?php
/* @var $channels */

use common\models\MeasureChannel;
use common\models\MeasureType;
use kartik\grid\GridView;

?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'Статистика каналов') ?></h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i></button>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <?php
        $gridColumns = [
            [
                'attribute' => 'title',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class',
                    'style' => 'text-align: center'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'measureTypeUuid',
                'vAlign' => 'middle',
                'mergeHeader' => true,
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
                'content' => function ($data) {
                    return $data['measureType']['title'];
                }
            ],
            [
                'attribute' => 'type',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
                'content' => function ($data) {
                    if ($data->type == MeasureType::MEASURE_TYPE_CURRENT)
                        return Yii::t('app', 'Текущий');
                    if ($data->type == MeasureType::MEASURE_TYPE_HOURS)
                        return Yii::t('app', 'Часовой');
                    if ($data->type == MeasureType::MEASURE_TYPE_DAYS)
                        return Yii::t('app', 'Дневной');
                    if ($data->type == MeasureType::MEASURE_TYPE_MONTH)
                        return Yii::t('app', 'По месяцам');
                    if ($data->type == MeasureType::MEASURE_TYPE_TOTAL)
                        return Yii::t('app', 'Накопительный');
                    if ($data->type == MeasureType::MEASURE_TYPE_INTERVAL)
                        return Yii::t('app', 'Интервальный');
                    if ($data->type == MeasureType::MEASURE_TYPE_TOTAL_CURRENT)
                        return Yii::t('app', 'Итоговый накопительный');
                    return "не определен";
                }
            ],
            [
                'vAlign' => 'middle',
                'mergeHeader' => true,
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
                'content' => function ($data) {
                    return $data->getFormatLastMeasure();
                }
            ],
        ];

        echo GridView::widget([
            'id' => 'requests-table',
            'dataProvider' => $channels,
            'columns' => $gridColumns,
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'headerRowOptions' => ['class' => 'kartik-sheet-style'],
            'filterRowOptions' => ['class' => 'kartik-sheet-style'],
            'beforeHeader' => [
                '{toggleData}'
            ],
            'toolbar' => [],
            'pjax' => true,
            'showPageSummary' => false,
            'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
            'summary' => '',
            'bordered' => true,
            'striped' => false,
            'condensed' => false,
            'responsive' => true,
            'persistResize' => false,
            'hover' => true,
            'panel' => [
                'type' => GridView::TYPE_PRIMARY
            ],
        ]);
        ?>
    </div>
</div>
