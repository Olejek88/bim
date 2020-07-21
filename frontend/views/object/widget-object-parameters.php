<?php
/* @var $parameters */

use kartik\grid\GridView;

?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'Параметры объекта') ?></h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i></button>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <?php
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
                'attribute' => 'parameterTypeUuid',
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'header' => Yii::t('app', 'Тип параметра'),
                'value' => function ($data) {
                    return $data['parameterType']['title'];
                }
            ],
            [
                'attribute' => 'value',
                'vAlign' => 'middle',
                'contentOptions' => [
                    'class' => 'table_class',
                    'style' => 'width: 100px; text-align: center;'
                ],
                'header' => Yii::t('app', 'Значение'),
                'mergeHeader' => true,
                'headerOptions' => ['class' => 'text-center'],
            ]
        ];

        echo GridView::widget([
            'dataProvider' => $parameters,
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
                'type' => GridView::TYPE_PRIMARY
            ]
        ]);
        ?>
    </div>
</div>
