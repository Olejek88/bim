<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\BalancePointSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tool-search box-padding">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    * @property string $measureChannelUuid
    * @property string $objectUuid
    * @property integer $input

    <?= $form->field($model, '_id') ?>

    <?= $form->field($model, 'uuid') ?>

    <?= $form->field($model, 'measureChannelUuid') ?>

    <?= $form->field($model, 'objectUuid') ?>

    <?= $form->field($model, 'input') ?>

    <?= $form->field($model, 'createdAt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Поиск'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Сбрость'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
