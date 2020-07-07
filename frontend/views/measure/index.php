<?php
/* @var $searchModel frontend\models\MeasureSearch
 * @var $channels
*/

use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Измерения');

$gridColumns = [
    [
        'attribute' => 'date',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'mergeHeader' => true,
        'content' => function ($data) {
            return date("d-m-Y", strtotime($data->date));
        }
    ],
    [
        'attribute' => 'measureChannelUuid',
        'vAlign' => 'middle',
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map($channels,
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'Любой'],
        'content' => function ($data) {
            return $data['measureChannel']->getFullTitle();
        }
    ],
    [
        'vAlign' => 'middle',
        'width' => '150px',
        'header' => 'Тип измерения',
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'content' => function ($data) {
            return $data['measureChannel']['measureType']['title'];
        }
    ],
    [
        'attribute' => 'value',
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
        'class' => 'kartik\grid\ActionColumn',
        'header' => 'Действия',
        'template'=> '{delete}',
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
            Html::a('Новый', ['/measure/create'], ['class' => 'btn btn-success'])
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
        'heading' => '<i class="glyphicon glyphicon-wrench"></i>&nbsp; Измерения',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);