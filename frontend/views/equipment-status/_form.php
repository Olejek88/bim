<?php

use app\commands\MainFunctions;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentStatus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-status-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-input-documentation',
        'options' => [
            'class' => 'form-horizontal col-lg-12 col-sm-12 col-xs-12',
            'enctype' => 'multipart/form-data'
        ],
    ]);
    ?>

    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->hiddenInput()->label(false);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]); ?>
    <?= '<br/><label id="error" style="color: red"></label>' ?>

    <div class="form-group text-center">

        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Обновить'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
