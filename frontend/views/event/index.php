<?php
/* @var $searchModel frontend\models\EventSearch */
/* @var $dataProvider */

/* @var Objects[] $objects */

use common\models\Objects;
use kartik\date\DatePicker;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\popover\PopoverX;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'ПолиТЭР::События объектов системы');

$gridColumns = [
    [
        'attribute' => '_id',
        'mergeHeader' => true,
        'hAlign' => 'center',
        'vAlign' => 'middle',
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
            return date("d-m-Y h:i:s", strtotime($data->date));
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'title',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => [
            'size' => 'lg'
        ],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'objectUuid',
        'vAlign' => 'middle',
        'width' => '180px',
        'value' => function ($data) {
            return $data->object->getFullTitle();
        },
        'filterType' => GridView::FILTER_SELECT2,
        'header' => Yii::t('app', 'Объект'),
        'filter' => $objects,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'format' => 'raw',
        'editableOptions' => function () use ($objects) {
            return [
                'header' => Yii::t('app', 'Объект'),
                'size' => 'lg',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'placement' => PopoverX::ALIGN_LEFT,
                'displayValueConfig' => $objects,
                'data' => $objects
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'mergeHeader' => true,
        'format' => 'raw',
        'attribute' => 'status',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'content' => function ($data) {
            if ($data['status'])
                return '<span class="label label-success">Выполнено</span>';
            else
                return '<span class="label label-info">В работе</span>';
        },
        'editableOptions' => function ($data) {
            $types = array(
                '0' => Yii::t('app', 'Выполнено'),
                '1' => Yii::t('app', 'В работе')
            );
            return [
                'header' => Yii::t('app', 'Статус'),
                'size' => 'sm',
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'placement' => PopoverX::ALIGN_LEFT,
                'displayValueConfig' => $types,
                'data' => $types,
                'formOptions' => [
                    'id' => 'id_' . $data->_id
                ],
                'options' => [
                    'id' => $data->_id,
                ]
            ];
        },
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'description',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => [
            'size' => 'lg'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return substr($data['description'], 0, 200);
        }
    ],
];

ob_start();
// форма указания периода
$form = ActiveForm::begin([
    'action' => ['event/index'],
    'method' => 'get',
]);
?>
<div class="row" style="margin-bottom: 8px; width:100%">
    <div class="col-sm-4" style="width:36%">
        <?php
        echo $form->field($searchModel, 'createTimeStart')->widget(DatePicker::class, [
            'removeButton' => false,
            'pjaxContainerId' => 'event',
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
            'pjaxContainerId' => 'event',
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
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'beforeHeader' => [
        '{toggleData}'
    ],
    'toolbar' => [
        ['content' =>
            Html::a(Yii::t('app', 'Новое'),
                ['/event/new', 'reference' => 'table'],
                [
                    'class' => 'btn btn-success',
                    'title' => Yii::t('app', 'Новое'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modalAdd'
                ])
        ],
        '{export}',
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'event'
    ],
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
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; ' . Yii::t('app', 'События объектов системы')

    ],
]);
$this->registerJs('$("#modalAdd").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
?>

<div class="modal remote fade" id="modalAdd">
    <div class="modal-dialog" style="width: 700px">
        <div class="modal-content loader-lg" id="modalContent">
        </div>
    </div>
</div>
