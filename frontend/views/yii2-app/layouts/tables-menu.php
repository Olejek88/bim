<div class="panel panel-default" style="float: left; width: 200px; padding: 3px">
    <?php

    use yii\helpers\Html;

    echo Html::a(Yii::t('app', 'Контрагенты'), ['../contragent/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Документация'), ['../documentation/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Объекты'), ['../objects/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';

    echo Html::a(Yii::t('app', 'Модели оборудования'), ['../equipment-model/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Каналы оповещений'), ['../user-channel/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Каналы сообщений'), ['../message-channel/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';

    echo Html::a(Yii::t('app', 'Шаблоны операций'), ['../operation-template/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Операции'), ['../operation/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Шаблоны этапов'), ['../stage-template/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Этапы'), ['../stage/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Шаблоны задач'), ['../task-template/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Задачи'), ['../task/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';

    echo Html::a(Yii::t('app', 'Типы атрибутов задач'), ['../event-attribute-type/create'], ['class' => 'btn btn-primary btn100']) . '<br/>';
    ?>
</div>
