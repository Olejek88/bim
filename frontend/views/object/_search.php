<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\ObjectsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="objects-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'objectTypeUuid') ?>

    <?= $form->field($model, 'objectSubTypeUuid') ?>

    <?= $form->field($model, 'parentUuid') ?>

    <?= $form->field($model, 'fiasGuid') ?>

    <?= $form->field($model, 'okato') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Поиск'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Сбросить'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
