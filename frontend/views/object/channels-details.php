<?php

/* @var $model Objects */
/* @var $channels */

use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\Objects;
use frontend\models\MeasureChannelSearch;
use kartik\grid\GridView;

$searchModel = new MeasureChannelSearch();
$channels = $searchModel->search(Yii::$app->request->queryParams);
$channels->pagination->pageSize = 0;
$channels->query->andWhere(['objectUuid' => $model->uuid]);

?>
<div id="<?= $model['_id'] ?>" class="kv-expand-row kv-grid-demo">
    <div class="kv-detail-content">
        <h3><?php echo $model->getFullTitle() ?>
        </h3>
        <div class="row">
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
                        return MeasureChannel::getFormatLastMeasureStatic($data['_id']);
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
</div>
