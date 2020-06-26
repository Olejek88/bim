<?php
/* @var $searchModel frontend\models\AlarmSearch */

/** @var $dataProvider */

use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'ПолиТЭР::Предупреждения');

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

ob_start();
// форма указания периода
$form = ActiveForm::begin([
    'action' => ['alarm/index'],
    'method' => 'get',
]);
?>
    <div class="row" style="margin-bottom: 8px; width:100%">
        <div class="col-sm-4" style="width:36%">
            <?php
            echo $form->field($searchModel, 'createTimeStart')->widget(DatePicker::class, [
                'removeButton' => false,
                'pjaxContainerId' => 'alarm',
                'options' => [
                    'class' => 'add-filter',
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ])->label(false);
            ?>
        </div>
        <div class="col-sm-4" style="width:36%">
            <?php
            echo $form->field($searchModel, 'createTimeEnd')->widget(DatePicker::class, [
                'removeButton' => false,
                'pjaxContainerId' => 'alarm',
                'options' => [
                    'class' => 'add-filter',
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]
            ])->label(false);
            ?>
        </div>
    </div>

<?php
ActiveForm::end();
$formHtml = ob_get_contents();
ob_end_clean();

echo GridView::widget([
    'filterSelector' => '.add-filter',
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
            'content' => $formHtml,
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
        'heading' => '<i class="fa fa-list"></i>&nbsp; ' . Yii::t('app', 'Журнал событий')
    ]
]);
