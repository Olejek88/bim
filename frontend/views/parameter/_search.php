<?php

use frontend\models\ParameterSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ParameterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-model-search box-padding">

    <?php $form = ActiveForm::begin(
        [
            'action' => ['index'],
            'method' => 'get',
        ]
    ); ?>

    <?php echo $form->field($model, '_id') ?>

    <?php echo $form->field($model, 'uuid') ?>

    <?php echo $form->field($model, 'entityUuid') ?>

    <?php echo $form->field($model, 'parameterTypeUuid') ?>

    <?php echo $form->field($model, 'value') ?>

    <?php echo $form->field($model, 'date') ?>

    <?php echo $form->field($model, 'createdAt') ?>

    <div class="form-group">
        <?php echo Html::submitButton(
            Yii::t('app', 'Поиск'),
            ['class' => 'btn btn-primary']
        ) ?>
        <?php echo Html::resetButton(
            Yii::t('app', 'Сбросить'),
            ['class' => 'btn btn-default']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
