<?php
/* @var $model ObjectType */

use common\models\ObjectType;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Типы объектов');
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
        'template' => '{delete}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'requests-table',
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a(Yii::t('app', 'Новый'),
                ['/object-type/new', 'reference' => 'table'],
                [
                    'class' => 'btn btn-success',
                    'title' => Yii::t('app', 'Новое'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modalAdd'
                ])
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
        'heading' => '<i class="fa fa-house"></i>&nbsp; Типы объектов',
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
