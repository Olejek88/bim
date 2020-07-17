<?php
/* @var $parameters common\models\Parameter */

/* @var $dataProvider
 * @var $entityUuid
 * @var $parameterTypes ParameterType []
 */

use common\models\ParameterType;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center"><?php echo Yii::t('app', 'Параметры') ?></h4>
</div>
<div class="modal-body">
    <?php
    $gridColumns = [
        [
            'attribute' => 'date',
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'contentOptions' => [
                'class' => 'table_class',
                'style' => 'width: 50px; text-align: center;'
            ],
            'headerOptions' => ['class' => 'text-center'],
            'content' => function ($data) {
                return date("d-m-Y H:i:s", strtotime($data->date));
            }
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
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
            },
            'editableOptions' => function () use ($parameterTypes) {
                $types = ArrayHelper::map($parameterTypes, 'uuid', 'title');
                return [
                    'header' => Yii::t('app', 'Типы атрибутов'),
                    'size' => 'lg',
                    'inputType' => Editable::INPUT_DROPDOWN_LIST,
                    'displayValueConfig' => $types,
                    'data' => $types
                ];
            },
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'value',
            'vAlign' => 'middle',
            'contentOptions' => [
                'class' => 'table_class',
                'style' => 'width: 100px; text-align: center;'
            ],
            'header' => Yii::t('app', 'Значение'),
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
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'header' => Yii::t('app', 'Действия'),
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'title' => Yii::t('app', 'Удалить'),
                        'data-confirm' => Yii::t('yii', 'Вы уверены, что хотите удалить этот элемент?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                        'data-query' => '',
                    ]);
                },
            ],
            'template' => '{link} {delete}'
        ]
    ];

    echo GridView::widget([
        'id' => 'event-table',
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
        'headerRowOptions' => ['class' => 'kartik-sheet-style'],
        'filterRowOptions' => ['class' => 'kartik-sheet-style'],
        'beforeHeader' => [
            '{toggleData}'
        ],
        'toolbar' => [
            ['content' =>
                Html::a(Yii::t('app', 'Новый'),
                    ['../parameter/new', 'entityUuid' => $entityUuid],
                    [
                        'class' => 'btn btn-success',
                        'title' => Yii::t('app', 'Новый'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalAddParameter'
                    ])
            ],
        ],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'enablePushState' => false,
            ],
        ],
        'showPageSummary' => false,
        'pageSummaryRowOptions' => ['style' => 'line-height: 0; padding: 0'],
        'summary' => '',
        'bordered' => true,
        'striped' => false,
        'condensed' => false,
        'responsive' => true,
        'persistResize' => false,
        'hover' => true,
    ]);
    $this->registerJs('$("#modalAddParameter").on("click", "button[data-number=modalAddParameter]",
    function() {
        $("#modalAddParameter").modal("hide");
    })');

    $this->registerJs('$("#modalAddParameter").on("hidden.bs.modal",
    function () {
        let ajax = document.getElementById("modalParameterContent");
        if (ajax.value) {
            ajax.value = false
            $.pjax.reload({
                container: "#parameter-table-pjax",
                url: "/parameter/list?uuid=' . $entityUuid . '",
                replace: false
            });
        }
        $(this).removeData();
    })');
    echo Html::hiddenInput("entityUuid", $entityUuid);
    ?>

    <div class="modal remote fade" id="modalAddParameter">
        <div class="modal-dialog" style="width: 800px">
            <div class="modal-content loader-lg" id="modalAddParameterContent">
            </div>
        </div>
    </div>
</div>
