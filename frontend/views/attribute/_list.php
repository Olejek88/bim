<?php
/* @var $alarms Alarm */

/* @var $objectUuid */

use common\models\Alarm;
use kartik\grid\GridView;
use yii\helpers\Html;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center"><?php echo Yii::t('app', 'Атрибуты объекта') ?></h4>
</div>
<div class="modal-body">
    <?php
    $gridColumns = [
        [
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'mergeHeader' => true,
            'contentOptions' => [
                'class' => 'table_class',
                'style' => 'width: 100px; text-align: center;'
            ],
            'headerOptions' => ['class' => 'text-center'],
            'content' => function ($data) {
                return date("d-m-Y h:i:s", strtotime($data->createdAt));
            }
        ],
        [
            'mergeHeader' => true,
            'attribute' => 'attributeType',
            'vAlign' => 'middle',
            'hAlign' => 'center',
            'content' => function ($data) {
                return $data->attributeType->title;
            },
        ],
        [
            'attribute' => 'title',
            'mergeHeader' => true,
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'headerOptions' => ['class' => 'text-center'],
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'header' => Yii::t('app', 'Действия'),
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    return Html::a('<span class="fa fa-trash"></span>', $url, [
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
        'id' => 'alarm-table',
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
                    ['../attribute/new', 'objectUuid' => $objectUuid],
                    [
                        'class' => 'btn btn-success',
                        'title' => Yii::t('app', 'Новое'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalAddAttribute'
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
    $this->registerJs('$("#modalAddAttribute").on("click", "button[data-number=modalAddAttribute]",
    function() {
        $("#modalAddAttribute").modal("hide");
    })');
    $this->registerJs('$("#modalAddAttribute").on("hidden.bs.modal",
    function () {
        $(this).removeData();
    })');
    $this->registerJs('$("#modalAddAttribute").on("hidden.bs.modal",
    function () {
        let ajax = document.getElementById("modalParameterContent");
        if (ajax.value) {
            ajax.value = false
            $.pjax.reload({
                container: "#alarm-table-pjax",
                url: "/attribute/list?uuid=' . $objectUuid . '",
                replace: false
            });
        }
        $(this).removeData();
    })');
    echo Html::hiddenInput("objectUuid", $objectUuid);
    ?>

    <div class="modal remote fade" id="modalAddAttribute">
        <div class="modal-dialog" style="width: 800px">
            <div class="modal-content loader-lg" id="modalAddAttributeContent">
            </div>
        </div>
    </div>
</div>
