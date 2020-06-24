<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AttributeType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="action-type-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'createdAt') ?>

    <?= $form->field($model, 'changedAt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Поиск'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Сбросить'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
