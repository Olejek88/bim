<?php
/* @var $events Event */

/* @var $objectUuid */

use common\models\Event;
use kartik\grid\GridView;
use yii\helpers\Html;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center"><?php echo Yii::t('app', 'Мероприятия объекта') ?></h4>
</div>
<div class="modal-body">
    <?php
    $gridColumns = [
        [
            'attribute' => 'createdAt',
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'contentOptions' => [
                'class' => 'table_class',
                'style' => 'width: 50px; text-align: center;'
            ],
            'headerOptions' => ['class' => 'text-center'],
            'content' => function ($data) {
                return date("d-m-Y h:i:s", strtotime($data->createdAt));
            }
        ],
        [
            'attribute' => 'title',
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'headerOptions' => ['class' => 'text-center'],
        ],
        [
            'attribute' => 'objectUuid',
            'vAlign' => 'middle',
            'width' => '180px',
            'value' => function ($data) {
                return $data['object']['title'];
            },
            'header' => Yii::t('app', 'Объект'),
            'format' => 'raw',
        ],
        [
            'attribute' => 'description',
            'contentOptions' => [
                'class' => 'table_class'
            ],
            'headerOptions' => ['class' => 'text-center'],
            'content' => function ($data) {
                return substr($data['description'], 0, 200);
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
                Html::a(Yii::t('app', 'Новое'),
                    ['../event/new', 'objectUuid' => $objectUuid],
                    [
                        'class' => 'btn btn-success',
                        'title' => Yii::t('app', 'Новое'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalAddEvent'
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
    $this->registerJs('$("#modalAddEvent").on("click", "button[data-number=modalAddEvent]",
    function() {
        $("#modalAddEvent").modal("hide");
    })');

    $this->registerJs('$("#modalAddEvent").on("hidden.bs.modal",
    function () {
        let ajax = document.getElementById("modalEventContent");
        if (ajax.value) {
            ajax.value = false
            $.pjax.reload({
                container: "#document-table-pjax",
                url: "/event/list?objectUuid=' . $objectUuid . '",
                replace: false
            });
        }
        $(this).removeData();
    })');
    echo Html::hiddenInput("objectUuid", $objectUuid);
    ?>

    <div class="modal remote fade" id="modalAddEvent">
        <div class="modal-dialog" style="width: 800px">
            <div class="modal-content loader-lg" id="modalAddEventContent">
            </div>
        </div>
    </div>
</div>
