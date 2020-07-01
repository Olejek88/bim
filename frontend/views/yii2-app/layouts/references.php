<div class="panel panel-default" style="float: left; width: 20%; padding: 3px">
    <?php

    use yii\helpers\Html;

    echo Html::a(Yii::t('app', 'Типы атрибутов'), ['../attribute-type'], ['class' => 'btn btn-warning btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Типы измерений'), ['../measure-type'], ['class' => 'btn btn-warning btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Типы объектов'), ['../object-type'], ['class' => 'btn btn-warning btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Подтипы объектов'), ['../object-sub-type'], ['class' => 'btn btn-warning btn100']) . '<br/>';
    echo Html::a(Yii::t('app', 'Типы параметров'), ['../parameter-type'], ['class' => 'btn btn-warning btn100']) . '<br/>';
    ?>
</div>
