<?php
/* @var $searchModel frontend\models\MeasureSearch
 * @var $channels
*/

use yii\grid\GridView;use yii\helpers\ArrayHelper;use yii\helpers\Html;use yii\helpers\Html;

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
            Html::a('Новый', ['/sensor-channel/create'], ['class' => 'btn btn-success'])
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
        'heading' => '<i class="glyphicon glyphicon-wrench"></i>&nbsp; Каналы измерения',
        'headingOptions' => ['style' => 'background: #337ab7']
    ],
]);

<?php

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\MeasureSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Измеренные значения');
?>
<div class="measured-value-index box-padding-index">

    <div class="box box-default">
        <div class="box-header with-border">
            <h2><?= Html::encode($this->title) ?></h2>
            <div class="box-tools pull-right">
                <span class="label label-default"></span>
            </div>
        </div>
        <div class="box-body" style="padding: 0 10px 0 10px;">
            <p>
                <?= Html::a(Yii::t('app', 'Новое измерение'), ['create'], ['class' => 'btn btn-success']) ?>
            </p>
            <div class="box-body-table">
                <h6 class="text-center">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => [
                            'class' => 'table-striped table table-bordered table-hover table-condensed'
                        ],
                        'columns' => [
                            [
                                'attribute' => '_id',
                                'contentOptions' => [
                                    'class' => 'table_class',
                                    'style' => 'width: 50px; text-align: center'
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                            ],
                            [
                                'attribute' => 'sensorChannel.device.fullTitle',
                                'contentOptions' => [
                                    'class' => 'table_class',
                                    'hAlign' => 'center',
                                    'style' => 'width: 200px'
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                            ],
                            [
                                'attribute' => 'value',
                                'contentOptions' => [
                                    'class' => 'table_class',
                                    'style' => 'width: 50px'
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                            ],
                            [
                                'attribute' => 'date',
                                'contentOptions' => [
                                    'class' => 'table_class',
                                    'style' => 'width: 100px'
                                ],
                                'headerOptions' => ['class' => 'text-center'],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Действия',
                                'headerOptions' => ['class' => 'text-center', 'width' => '70'],
                                'contentOptions' => [
                                    'class' => 'text-center',
                                ],
                                'template' => '{view} {update} {delete}{link}',
                            ],
                        ],
                    ]); ?>
                </h6>
            </div>
        </div>
    </div>
</div>
