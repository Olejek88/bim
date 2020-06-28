<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\MeasureChannelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-request-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'equipmentUuid') ?>

    <?= $form->field($model, 'measureTypeUuid') ?>

    <?= $form->field($model, 'path') ?>

    <?= $form->field($model, 'original_name') ?>

    <?= $form->field($model, 'param_id') ?>

    <?= $form->field($model, 'type') ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
