<?php
/* @var $searchModel frontend\models\EventSearch */
/* @var $dataProvider */

/* @var Objects[] $objects */

use common\models\Objects;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\popover\PopoverX;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ПолиТЭР::События объектов системы');

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'title',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => [
            'size' => 'lg'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'objectUuid',
        'vAlign' => 'middle',
        'width' => '180px',
        'value' => function ($data) {
            return $data['objectUuid']['title'];
        },
        'filterType' => GridView::FILTER_SELECT2,
        'header' => Yii::t('app', 'Объект'),
        'filter' => $objects,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'format' => 'raw',
        'editableOptions' => function () use ($objects) {
            return [
                'header' => Yii::t('app', 'Объект'),
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'placement' => PopoverX::ALIGN_LEFT,
                'displayValueConfig' => $objects,
                'data' => $objects
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'description',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => [
            'size' => 'lg'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return substr($data['description'], 0, 200);
        }
    ],
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a(Yii::t('app', 'Новое'),
                ['/event/new', 'reference' => 'table'],
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
        'target' => GridView::TARGET_BLANK,
        'filename' => 'event'
    ],
    'pjax' => true,
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary' => '',
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'floatHeader' => false,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; ' . Yii::t('app', 'События объектов системы')

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
