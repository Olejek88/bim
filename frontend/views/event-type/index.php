<?php
/* @var $searchModel EventSearchType */
/* @var $types */
/* @var $dataProvider */

use frontend\models\EventSearchType;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\popover\PopoverX;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ПолиТЭР::Типы мероприятий');
$sources = ['0' => 'Общий', '1' => 'Тепло', '2' => 'Вода', '3' => 'Электроэнергия'];
$gridColumns = [
    [
        'attribute' => '_id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['_id'];
        }
    ],
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
        'attribute' => 'source',
        'vAlign' => 'middle',
        'filterType' => GridView::FILTER_SELECT2,
        'header' => Yii::t('app', 'Энергоресурс'),
        'filter' => $sources,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'format' => 'raw',
        'editableOptions' => function () use ($sources) {
            return [
                'header' => Yii::t('app', 'Энергоресурс'),
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'placement' => PopoverX::ALIGN_LEFT,
                'displayValueConfig' => $sources,
                'data' => $sources
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'parameterTypeUuid',
        'vAlign' => 'middle',
        'value' => function ($data) {
            if ($data->parameterTypeUuid) {
                return $data->parameterType->title;
            } else {
                return "";
            }
        },
        'filterType' => GridView::FILTER_SELECT2,
        'header' => Yii::t('app', 'Тип'),
        'filter' => $types,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'format' => 'raw',
        'editableOptions' => function () use ($types) {
            return [
                'header' => Yii::t('app', 'Тип'),
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'placement' => PopoverX::ALIGN_LEFT,
                'displayValueConfig' => $types,
                'data' => $types
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'cnt_effect',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => [
            'size' => 'lg'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'header' => Yii::t('app', 'Действия'),
        'template' => '{delete}'
    ]
];
try {
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
                Html::a(Yii::t('app', 'Новый'),
                    ['/attribute-type/new', 'reference' => 'table'],
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
        'pjaxSettings' => [
            'options' => [
                'id' => 'event',
            ],
        ],
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
            'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; ' . Yii::t('app', 'Типы мероприятий')
        ],
    ]);
} catch (Exception $exception) {
    echo $exception;
}
$this->registerJs('$("#modalAdd").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
?>

<div class="modal remote fade" id="modalAdd">
    <div class="modal-dialog" style="width: 800px; height: 400px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
