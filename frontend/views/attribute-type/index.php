<?php
/* @var $searchModel AttributeSearchType */

use frontend\models\AttributeSearchType;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ТОИРУС::Типы атрибутов');

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'title',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => [
            'asPopover' => false,
            'size' => 'lg'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data->name;
        }
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
                Html::a(Yii::t('app', 'Новый'), ['/attribute-type/create'], ['class' => 'btn btn-success']),
                Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => Yii::t('app', 'Сбросить')])
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
            'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; ' . Yii::t('app', 'Типы атрибутов')
        ],
    ]);
} catch (Exception $exception) {

}
