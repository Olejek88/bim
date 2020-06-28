<?php
/* @var $dataProvider */

/* @var $searchModel ParameterTypeSearch */

use frontend\models\ParameterTypeSearch;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Типы параметров');

$gridColumns = [
    [
        'attribute' => 'uuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'mergeHeader' => true,
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'title',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'buttons' => [
            'edit' => function ($url, $model) {
                $url = Yii::$app->getUrlManager()->createUrl(['../parameter-type/edit', 'id' => $model['_id']]);
                return Html::a('<span class="fa fa-edit"></span>', $url,
                    [
                        'title' => Yii::t('app', 'Редактировать'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalAdd',
                    ]);
            },
        ],
        'template' => '{delete} {edit}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'parameter-type-table',
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
        'heading' => '<i class="glyphicon glyphicon-wrench"></i>&nbsp; Типы параметров',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

$this->registerJs('$("#modalAdd").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
?>

<div class="modal remote fade" id="modalAdd">
    <div class="modal-dialog" style="width: 700px">
        <div class="modal-content loader-lg" id="modalContent">
        </div>
    </div>
</div>
