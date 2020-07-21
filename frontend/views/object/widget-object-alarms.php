<?php

/* @var $alarms */

use kartik\grid\GridView;

?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'Предупреждения') ?></h3>
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
    <div class="box-body">
        <?php
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
            ]
        ];

        echo GridView::widget([
            'filterSelector' => '.add-filter',
            'dataProvider' => $alarms,
            'columns' => $gridColumns,
            'headerRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px'],
            'filterRowOptions' => ['class' => 'kartik-sheet-style', 'style' => 'height: 20px important!'],
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'beforeHeader' => [
                '{toggleData}'
            ],
            'toolbar' => [],
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
