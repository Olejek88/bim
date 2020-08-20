<?php
/* @var $events */

use kartik\grid\GridView;

?>
<style>
    .grid-view td {
        white-space: pre-line;
    }
</style>
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'События') ?></h3>
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
                'mergeHeader' => true,
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'content' => function ($data) {
                    return $data->object->getFullTitle();
                },
            ],
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
                },
            ],
            [
                'attribute' => 'eventTypeUuid',
                'vAlign' => 'middle',
                'value' => function ($data) {
                    return $data->eventType->title;
                },
                'header' => Yii::t('app', 'Тип'),
                'format' => 'raw'
            ],
            [
                'attribute' => 'title',
                'contentOptions' => [
                    'class' => 'table_class'
                ],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'status',
                'vAlign' => 'middle',
                'hAlign' => 'center',
                'content' => function ($data) {
                    if ($data['status'] == 1) {
                        return '<span class="label label-success">Выполнено</span>';
                    } else {
                        return '<span class="label label-info">В работе</span>';
                    }
                },
            ]
        ];

        echo GridView::widget([
            'filterSelector' => '.add-filter',
            'dataProvider' => $events,
            'columns' => $gridColumns,
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'beforeHeader' => [
                '{toggleData}'
            ],
            'toolbar' => [],
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
                'type' => GridView::TYPE_PRIMARY

            ],
        ]);
        ?>
    </div>
</div>
