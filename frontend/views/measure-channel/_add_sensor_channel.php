<?php
/* @var $model common\models\MeasureChannel */
/* @var $objects */
/* @var $measureChannels */

/* @var $types */

use common\components\MainFunctions;
use common\models\Device;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '/measure-channel/save',
    'options' => [
        'id' => 'formMeasureChannel'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Добавить канал измерения</h4>
</div>
<div class="modal-body">
    <?php
    if ($model['uuid']) {
        echo Html::hiddenInput("sensorUuid", $model['uuid']);
        echo $form->field($model, 'uuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else
        echo $form->field($model, 'uuid')->hiddenInput(['value' => MainFunctions::GUID()])->label(false);
    ?>

    <?php
    echo $form->field($model, 'title')->textInput(['maxlength' => true]);
    echo $form->field($model, 'objectUuid')->widget(Select2::class,
        [
            'data' => $objects,
            'options' => ['placeholder' => Yii::t('app', 'Выберите объект ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    echo $form->field($model, 'measureTypeUuid')->widget(Select2::class,
        [
            'data' => $types,
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип измерения ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);

    echo $form->field($model, 'type')->widget(Select2::class,
        [
            'data' => ['0' => 'Текущие', '1' => 'Часовой', '2' => 'Дневной'],
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    echo $form->field($model, 'path')->textInput(['maxlength' => true]);
    echo $form->field($model, 'original_name')->textInput(['maxlength' => true]);
    echo $form->field($model, 'param_id')->textInput(['maxlength' => true]);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).on("beforeSubmit", "#formMeasureChannel", function () {
        $.ajax({
            url: "../measure-channel/save",
            type: "post",
            data: $('#formMeasureChannel').serialize(),
            success: function () {
                $('#modalAdd').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', function (e) {
        e.preventDefault();
    });
</script>
<?php ActiveForm::end(); ?>
