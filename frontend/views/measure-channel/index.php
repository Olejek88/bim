<?php
/* @var $searchModel frontend\models\MeasureChannelSearch */

/* @var $dataProvider */

use common\models\MeasureType;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Каналы измерения');

$gridColumns = [
    [
        'class' => 'kartik\grid\EditableColumn',
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
        'attribute' => 'equipmentUuid',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map($equipment, 'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'content' => function ($data) {
            return $data['equipment']->getFullTitle();
        }
    ],
    [
        'attribute' => 'measureTypeUuid',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map($types, 'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'content' => function ($data) {
            return $data['measureType']['title'];
        }
    ],
    [
        'vAlign' => 'middle',
        'width' => '200px',
        'attribute' => 'original_name',
        'header' => 'Оригинальное название',
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'param_id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'attribute' => 'type',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => [
            MeasureType::MEASURE_TYPE_CURRENT => Yii::t('app', 'Текущий'),
            MeasureType::MEASURE_TYPE_HOURS => Yii::t('app', 'Часовой'),
            MeasureType::MEASURE_TYPE_DAYS => Yii::t('app', 'Дневной'),
            MeasureType::MEASURE_TYPE_MONTH => Yii::t('app', 'По месяцам'),
            MeasureType::MEASURE_TYPE_TOTAL => Yii::t('app', 'Накопительный'),
            MeasureType::MEASURE_TYPE_INTERVAL => Yii::t('app', 'Интервальный'),
            MeasureType::MEASURE_TYPE_TOTAL_CURRENT => Yii::t('app', 'Итоговый накопительный'),
        ],
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
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
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'buttons' => [
            'edit' => function ($url, $model) {
                $url = Yii::$app->getUrlManager()->createUrl(['../measure-channel/edit', 'id' => $model['_id']]);
                return Html::a('<span class="fa fa-edit"></span>', $url,
                    [
                        'title' => Yii::t('app', 'Редактировать'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalEdit',
                    ]);
            },
        ],
        'template' => '{delete} {edit}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'requests-table',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a('Новый', ['/measure-channel/new'], ['class' => 'btn btn-success'])
        ],
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'requests'
    ],
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
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-wrench"></i>&nbsp; Каналы измерения',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

$this->registerJs('$("#modalEdit").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
?>

<div class="modal remote fade" id="modalEdit">
    <div class="modal-dialog" style="width: 700px">
        <div class="modal-content loader-lg" id="modalContent">
        </div>
    </div>
</div>
