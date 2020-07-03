<?php
/* @var $searchModel frontend\models\UsersSearch
 * @var $dataProvider
 */

use common\models\User;
use kartik\editable\Editable;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ПолиТЭР::Управление пользователями');

$gridColumns = [
    [
        'attribute' => 'id',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center;'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'mergeHeader' => true,
        'content' => function ($data) {
            return Html::a($data->id, ['timeline', 'id' => $data['id']]);
        }
    ],
    [
        'attribute' => 'image',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class',
            'style' => 'width: 50px; text-align: center;'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'header' => Yii::t('app', 'Аватар'),
        'mergeHeader' => true,
        'content' => function ($data) {
            return '<img src="' . $data->getImageUrl() . '" class="user-image" alt="U">';
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'name',
        'vAlign' => 'middle',
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'headerOptions' => ['class' => 'text-center'],
        'header' => Yii::t('app', 'Логин'),
        'editableOptions' => [
            'size' => 'lg',
        ],
        'content' => function ($data) {
            return $data->name;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'username',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => Yii::t('app', 'Имя пользователя'),
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'format' => 'raw',
        'contentOptions' => [
            'class' => 'table_class'
        ],
    ],
    [
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'header' => Yii::t('app', 'Права доступа'),
        'hAlign' => 'center',
        'mergeHeader' => true,
        'headerOptions' => ['class' => 'text-center'],
        'content' => function ($data) {
            $assignments = Yii::$app->getAuthManager()->getAssignments($data['id']);
            $rights = '';
            foreach ($assignments as $value) {
                if ($value->roleName == User::ROLE_ADMIN)
                    $rights .= '<span class="label label-danger">' . Yii::t('app', 'Администратор') . '</span>';
                if ($value->roleName == User::ROLE_OPERATOR)
                    $rights .= '<span class="label label-success">' . Yii::t('app', 'Оператор') . '</span>';
            }
            return $rights;
        }
    ],
    [
        'class' => 'kartik\grid\EditableColumn',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'header' => 'e-mail',
        'format' => 'raw',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'mergeHeader' => true,
        'contentOptions' => [
            'class' => 'table_class'
        ],
        'editableOptions' => function ($data) {
            return [
                'inputType' => Editable::INPUT_TEXT,
                'value' => $data->email,
                'header' => 'E-mail',
                'name' => 'email',
            ];
        },
    ],
    [
        'attribute' => 'status',
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'middle',
        'filter' => [
            9 => 'Не активен',
            10 => 'Активен',
            0 => 'Удален'
        ],
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Любой')],
        'filterType' => GridView::FILTER_SELECT2,
        'header' => Yii::t('app', 'Статус'),
        'value' => function ($model, $key, $index, $column) {
            if ($model->status == User::STATUS_DELETED)
                return Html::tag('span', 'Удален', ['class' => 'label label-danger']);
            if ($model->status == User::STATUS_ACTIVE)
                return Html::tag('span', 'Активен', ['class' => 'label label-success']);
            return Html::tag('span', 'Не активен', ['class' => 'label label-warning']);
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'header' => Yii::t('app', 'Действия'),
        'buttons' => [
            'edit' => function ($url, $model) {
                $url = Yii::$app->getUrlManager()->createUrl(['../user/edit', 'id' => $model['id']]);
                return Html::a('<span class="fa fa-edit"></span>', $url,
                    [
                        'title' => Yii::t('app', 'Редактировать'),
                        'data-toggle' => 'modal',
                        'data-target' => '#modalEditUsers',
                    ]);
            },
        ],
        'template' => '{edit} {delete}',
        'headerOptions' => ['class' => 'kartik-sheet-style'],
    ]
];

echo GridView::widget([
    'id' => 'users-table',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
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
                ['/user/new'],
                [
                    'class' => 'btn btn-success',
                    'title' => Yii::t('app', 'Новый'),
                    'data-toggle' => 'modal',
                    'data-target' => '#modalEditUsers'
                ])
        ],
        '{export}',
    ],
    'export' => [
        'fontAwesome' => true,
        'target' => GridView::TARGET_BLANK,
        'filename' => 'users'
    ],
    'pjax' => true,
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
        'heading' => '<i class="glyphicon glyphicon-tags"></i>&nbsp; ' . Yii::t('app', 'Пользователи')
    ],
]);

$this->registerJs('$("#modalEditUsers").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');

?>

<div class="modal remote fade" id="modalEditUsers">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalContentEditUsers">
        </div>
    </div>
</div>
