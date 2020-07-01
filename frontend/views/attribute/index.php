<?php

use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $searchModel frontend\models\AttributeSearch */
/* @var $dataProvider */
/* @var $attributeTypes */

$this->title = Yii::t('app', 'Атрибуты');

$gridColumns = [
    [
        'attribute' => 'createdAt',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 100px; text-align: center;'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'title',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'attributeTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map($attributeTypes, 'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'value' => function ($data) {
            return $data['title'];
        },
        'editableOptions' => function () use ($attributeTypes) {
            $types = ArrayHelper::map($attributeTypes, 'uuid', 'title');
            return [
                'header' => Yii::t('app', 'Типы атрибутов'),
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'displayValueConfig' => $types,
                'data' => $types
            ];
        },
    ],
    [
        'attribute' => 'entityUuid',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'header' => Yii::t('app', 'Связь'),
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->getEntityTitle();
        }
    ]
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'headerRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px important!'],
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        [
            'options' => ['style' => 'width:100%']
        ],
    ],
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'alarm',
        ],
    ],
    'showPageSummary' => false,
    'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
    'summary' => '',
    'bordered' => true,
    'striped' => false,
    'condensed' => true,
    'responsive' => false,
    'hover' => true,
    'floatHeader' => false,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="fa fa-list"></i>&nbsp; ' . Yii::t('app', 'Атрибуты')
    ]
]);
