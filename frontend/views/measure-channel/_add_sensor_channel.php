<?php
/* @var $model common\models\MeasureChannel */
/* @var $objects */
/* @var $object_uuid */
/* @var $measureChannels */
/* @var $dataSources */

/* @var $types */

use common\components\MainFunctions;
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
    <h4 class="modal-title">Канал измерения</h4>
</div>
<div class="modal-body">
    <?php
    if ($model['uuid']) {
        echo Html::hiddenInput("channelUuid", $model['uuid']);
        echo $form->field($model, 'uuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else
        echo $form->field($model, 'uuid')->hiddenInput(['value' => MainFunctions::GUID()])->label(false);
    ?>

    <?php
    echo $form->field($model, 'title')->textInput(['maxlength' => true]);
    if (!empty($object_uuid)) {
        echo $form->field($model, 'objectUuid')->hiddenInput(['value' => $object_uuid])->label(false);
    } else {
        if ($model['uuid']) {
            echo $form->field($model, 'objectUuid')->hiddenInput(['value' => $model['objectUuid']])->label(false);
        } else {
            echo $form->field($model, 'objectUuid')->widget(Select2::class,
                [
                    'data' => $objects,
                    'options' => ['placeholder' => Yii::t('app', 'Выберите объект ...')],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
        }
    }
    echo $form->field($model, 'measureTypeUuid')->widget(Select2::class,
        [
            'data' => $types,
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип измерения ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo $form->field($model, 'type')->widget(Select2::class,
        [
            'data' => ['0' => 'Текущие', '1' => 'Часовой', '2' => 'Дневной', '4' => 'По месяцам', '7' => 'На дату'],
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    echo $form->field($model, 'path')->textInput(['maxlength' => true]);
    echo $form->field($model, 'original_name')->textInput(['maxlength' => true]);
    echo $form->field($model, 'param_id')->textInput(['maxlength' => true]);
    echo $form->field($model, 'data_source')->widget(Select2::class,
        [
            'data' => $dataSources,
            'options' => ['placeholder' => Yii::t('app', 'Выберите источник ...')],
            'pluginOptions' => [
            ],
        ]);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<script>
    $(document).one("beforeSubmit", "#formMeasureChannel", function (e) {
        e.preventDefault();
    }).one('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "../measure-channel/save",
            type: "post",
            data: $('#formMeasureChannel').serialize(),
            success: function () {
                $('#modalAdd').modal('hide');
            },
            error: function () {
            }
        });
    });
</script>
<?php ActiveForm::end(); ?>
