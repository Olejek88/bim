<?php
/* @var $searchModel ObjectsSearch */
/* @var $objectTypes */
/* @var $objectSubTypes */

use common\models\MeasureChannel;
use frontend\models\ObjectsSearch;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ПолиТЭР::Объекты');

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
        'class' => 'kartik\grid\ExpandRowColumn',
        'width' => '50px',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => '',
        'value' => function () {
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function ($model) {
            // TODO перенести в контроллер
            $channels = MeasureChannel::find()->where(['objectUuid' => $model->uuid])->all();
            return Yii::$app->controller->renderPartial('channels-details', ['model' => $model,
                'channels' => $channels]);
        },
        'expandIcon' => '<span class="glyphicon glyphicon-expand"></span>',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'expandOneOnly' => true
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'title',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data['title'];
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'objectTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => $objectTypes,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'value' => function ($data) {
            return $data['objectType']['title'];
        },
        'editableOptions' => function () use ($objectTypes) {
            return [
                'header' => Yii::t('app', 'Тип объекта'),
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $objectTypes,
                'data' => $objectTypes
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'objectSubTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => $objectTypes,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'value' => function ($data) {
            return $data['objectSubType']['title'];
        },
        'editableOptions' => function () use ($objectSubTypes) {
            return [
                'header' => Yii::t('app', 'Подтип объекта'),
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $objectSubTypes,
                'data' => $objectSubTypes
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'fiasGuid',
        'width' => '100px',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center']
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'fiasParentGuid',
        'width' => '100px',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center']
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'okato',
        'width' => '100px',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center']
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'width' => '150px',
        'header' => Yii::t('app', 'Действия'),
        'buttons' => [
            'register' => function ($url, $model) {
                return Html::a('<span class="fa fa-list"></span>',
                    ['/register/list', 'uuid' => $model['uuid']],
                    [
                        'title' => Yii::t('app', 'Журнал событий'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalRegister',
                    ]
                );
            },
            'channels' => function ($url, $model) {
                return Html::a('<span class="fa fa-bar-chart"></span>',
                    ['/object/channels', 'uuid' => $model['uuid']],
                    [
                        'title' => Yii::t('app', 'Каналы измерения'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalChannels',
                    ]
                );
            },
            'events' => function ($url, $model) {
                return Html::a('<span class="fa fa-calendar"></span>',
                    ['/object/events', 'uuid' => $model['uuid']],
                    [
                        'title' => Yii::t('app', 'События'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalEvents',
                    ]
                );
            },
            'edit' => function ($url, $model) {
                return Html::a('<span class="fa fa-edit"></span>',
                    ['/object/edit', 'uuid' => $model['uuid']],
                    [
                        'title' => Yii::t('app', 'Редактировать'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalAddEquipment',
                    ]);
            },
            'dashboard' => function ($url, $model) {
                return Html::a('<span class="fa fa-pie-chart"></span>&nbsp',
                    ['/object/dashboard', 'uuid' => $model['uuid']]);
            }
        ],
        'template' => '{edit} {register} {channels} {events} {dashboard} {delete}'
    ]
];

echo GridView::widget([
    'id' => 'equipment-table-index',
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
            Html::a(Yii::t('app', 'Новый'),
                ['/object/new'],
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
        'id' => 'ww',
        'target' => GridView::TARGET_BLANK,
        'filename' => 'objects'
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
        'heading' => '<i class="fa fa-house"></i>&nbsp;' . Yii::t('app', 'Объекты')
    ],
    'rowOptions' => function ($model) {
        $uuid = Yii::$app->request->getQueryParam('uuid');
        if ($uuid) {
            if ($uuid == $model['uuid'])
                return ['class' => 'danger'];
        }
    }
]);

$this->registerJs('$("#modalRegister").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalChannels").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalEvents").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalAdd").on("show.bs.modal",
function () {
    var w0 = document.getElementById(\'w0\');
    if (w0) {
      w0.id = \'w1\';
    }
})');
$this->registerJs('$("#modalAdd").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
?>

<div class="modal remote fade" id="modalRegister">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content loader-lg" id="modalDocumentationContent">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalAdd">
    <div class="modal-dialog" style="width: 800px; height: 400px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalChannels">
    <div class="modal-dialog" style="width: 1200px">
        <div class="modal-content loader-lg">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalRegister">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content loader-lg" id="modalRegisterContent">
        </div>
    </div>
</div>
