<?php

/* @var $dataProvider */

use kartik\grid\GridView;

$this->title = Yii::t('app', 'ПолиТЭР::Районы');

$gridColumns = [
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
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $objectsName = '';
            $objects = $data->getObjectsByDistrict(3);
            foreach ($objects as $object) {
                $objectsName .= $object->getFullTitle() . '; ';
            }
            return $objectsName;
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'width' => '50px',
        'header' => Yii::t('app', 'Действия'),
        'template' => '{delete}'
    ]
];

echo GridView::widget([
    'id' => 'district-table-index',
    'dataProvider' => $dataProvider,
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
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="fa fa-house"></i>&nbsp;' . Yii::t('app', 'Районы')
    ]
]);
