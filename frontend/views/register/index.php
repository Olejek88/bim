<?php
/* @var $searchModel frontend\models\RegisterSearch */

use common\models\Register;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'ПолиТЭР::Журнал объектов');

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
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'type',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => [
            Register::TYPE_INFO => Yii::t('app', 'Информация'),
            Register::TYPE_WARNING => Yii::t('app', 'Предупреждение'),
            Register::TYPE_ERROR => Yii::t('app', 'Ошибка')
        ],
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'content' => function ($data) {
            if ($data->type == Register::TYPE_INFO)
                return "<span class='badge' style='background-color: green; height: 12px; margin-top: -3px'> </span>&nbsp;Информация";
            if ($data->type == Register::TYPE_WARNING)
                return "<span class='badge' style='background-color: orange; height: 12px; margin-top: -3px'> </span>&nbsp;Предупреждение";
            if ($data->type == Register::TYPE_ERROR)
                return "<span class='badge' style='background-color: red; height: 12px; margin-top: -3px'> </span>&nbsp;Ошибка";
            return "тип не определен";
        }
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
    'action' => ['service-register/index'],
    'method' => 'get',
]);
?>
    <div class="row" style="margin-bottom: 8px; width:100%">
        <div class="col-sm-4" style="width:36%">
            <?php
            echo $form->field($searchModel, 'createTimeStart')->widget(DatePicker::class, [
                'removeButton' => false,
                'pjaxContainerId' => 'register',
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
                'pjaxContainerId' => 'register',
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
            'id' => 'register',
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
        'heading' => '<i class="fa fa-list"></i>&nbsp; ' . Yii::t('app', 'Журнал событий объектов системы')
    ]
]);
