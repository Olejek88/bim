<?php
/* @var $dataProvider */

/* @var $searchModel frontend\models\BalancePointSearch */


use common\models\BalancePoint;
use common\models\MeasureChannel;
use common\models\Objects;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ПолиТЭР::Точки балансов');

$measureChannels = MeasureChannel::find()->orderBy('title')->asArray()->all();

$gridColumns = [
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'attribute' => 'measureChannelUuid',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'header' => Yii::t('app', 'Канал измерения'),
        'headerOptions' => ['class' => 'text-center'],
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map($measureChannels, 'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'content' => function ($data) {
            return $data['measureChannel']['title'];
        }
    ],
    [
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'header' => Yii::t('app', 'Местоположение'),
        'attribute' => 'objectUuid',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Objects::find()->where(['deleted' => false])->orderBy('title')->all(),
            'uuid', 'title'),
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            return $data['object']['title'];
        }
    ],
    [
        'header' => Yii::t('app', 'Вход/выход'),
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'mergeHeader' => true,
        'content' => function ($data) {
            if ($data['input'] == BalancePoint::CONSUMER)
                return 'Потребитель';
            if ($data['input'] == BalancePoint::INPUT)
                return 'Вход';
            if ($data['input'] == BalancePoint::OUTPUT)
                return 'Выход';
            return 'Ошибка';
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => Yii::t('app', 'Действия'),
        'buttons' => [
            'edit' => function ($url, $model) {
                return Html::a('<span class="fa fa-edit"></span>',
                    ['/balance-point/edit', 'uuid' => $model['uuid'], 'reference' => 'table'],
                    [
                        'title' => Yii::t('app', 'Редактировать'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalAddPoint',
                    ]);
            },
            'delete' => function ($url, $model, $key) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url . '&oq=' . urlencode($_SERVER['REQUEST_URI']), [
                    'title' => Yii::t('yii', 'Удалить'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'data-query' => '',
                ]);
            },
        ],
        'template' => '{edit} {delete}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

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
        [
            'content' => $formHtml,
        ],
    ],
    'export' => [
        'target' => GridView::TARGET_BLANK,
        'filename' => 'event'
    ],
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'id' => 'defect-table',
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
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<i class="glyphicon glyphicon-calendar"></i>&nbsp; ' . Yii::t('app', 'Точки балансов')

    ],
]);

$this->registerJs('$("#modalAddPoint").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
?>

<div class="modal remote fade" id="modalAddPoint">
    <div class="modal-dialog">
        <div class="modal-content loader-lg"></div>
    </div>
</div>
