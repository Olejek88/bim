<?php
/* @var $model common\models\MeasureChannel */
/* @var $equipments */
/* @var $measureChannels */

/* @var $types */

use common\components\MainFunctions;
use common\models\Device;
use common\models\User;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
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
    echo $form->field($model, 'equipmentUuid')->widget(Select2::class,
        [
            'name' => 'kv_types',
            'language' => Yii::t('app', 'ru'),
            'data' => $measureChannels,
            'options' => ['placeholder' => Yii::t('app', 'Выберите оборудование ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    echo $form->field($model, 'measureTypeUuid')->widget(Select2::class,
        [
            'name' => 'kv_types',
            'language' => Yii::t('app', 'ru'),
            'data' => $measureChannels,
            'options' => ['placeholder' => Yii::t('app', 'Выберите канал ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);

    echo $form->field($model, 'type',
        ['template' => MainFunctions::getAddButton("/measure-type/create")])->widget(Select2::class,
        [
            'name' => 'kv_types',
            'language' => Yii::t('app', 'ru'),
            'data' => $types,
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    echo $form->field($model, 'path')->textInput(['maxlength' => true]);
    echo $form->field($model, 'original_name')->textInput(['maxlength' => true]);
    echo $form->field($model, 'param_id')->textInput(['maxlength' => true]);

    ?>

    <?php
    if (isset($deviceUuid)) {
        echo $form->field($model, 'deviceUuid')->hiddenInput(['value' => $deviceUuid])->label(false);
    } else {
        $device = Device::find()->where(['deleted' => 0])->all();
        $items = ArrayHelper::map($device, 'uuid', function ($data) {
            return $data->getFullTitle();
        });
        echo $form->field($model, 'deviceUuid',
            ['template' => MainFunctions::getAddButton("/device/create")])->widget(Select2::class,
            [
                'data' => $items,
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите оборудование..'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }
    ?>

    <?php echo $form->field($model, 'oid')->hiddenInput(['value' => User::getOid(Yii::$app->user->identity)])->label(false); ?>
    <?= $form->field($model, 'register')->textInput() ?>
    <?php echo Html::hiddenInput("type", "channel"); ?>
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
                $('#modalEdit').modal('hide');
            },
            error: function () {
            }
        })
    }).on('submit', function (e) {
        e.preventDefault();
    });
</script>
<?php ActiveForm::end(); ?>
