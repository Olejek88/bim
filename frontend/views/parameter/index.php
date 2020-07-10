<?php

use common\models\ParameterType;
use frontend\models\ParameterSearch;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $searchModel ParameterSearch
 * /* @var $dataProvider
 * @var $parameterTypes ParameterType []
 */

$this->title = Yii::t('app', 'Вычисляемые параметры');

$gridColumns = [
    [
        'attribute' => 'date',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 100px; text-align: center;'
        ],
        'mergeHeader' => true,
        'header' => Yii::t('app', 'Дата'),
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'parameterTypeUuid',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map($parameterTypes, 'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'header' => Yii::t('app', 'Тип параметра'),
        'value' => function ($data) {
            return $data['parameterType']['title'];
        },
        'editableOptions' => function () use ($parameterTypes) {
            $types = ArrayHelper::map($parameterTypes, 'uuid', 'title');
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
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'value',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 100px; text-align: center;'
        ],
        'header' => Yii::t('app', 'Значение'),
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
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
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'header' => Yii::t('app', 'Действия'),
        'template' => '{delete}'
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
        'heading' => '<i class="fa fa-equation"></i>&nbsp;Вычисляемые параметры'
    ]
]);
