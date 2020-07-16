<?php

/* @var $attribute */
/* @var $objectUuid */

/* @var $objects */

use common\components\MainFunctions;
use common\models\AttributeType;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute('/attribute/validation'),
    'action' => '../attribute/save',
    'options' => [
        'id' => 'addAttributeForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить атрибут') ?></h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($attribute, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);
    $attributeTypes = AttributeType::find()->all();
    $items = ArrayHelper::map($attributeTypes, 'uuid', 'title');
    echo $form->field($attribute, 'attributeTypeUuid')->widget(Select2::class,
        [
            'name' => 'kv_types',
            'language' => Yii::t('app', 'ru'),
            'data' => $items,
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип атрибута ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    echo $form->field($attribute, 'title')->textInput(['maxlength' => true]);
    if ($attribute['entityUuid'] != null) {
        echo $form->field($attribute, 'entityUuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else {
        if ($objectUuid) {
            echo $form->field($attribute, 'entityUuid')->hiddenInput(['value' => $objectUuid])->label(false);
        } else {
            echo $form->field($attribute, 'entityUuid')->widget(Select2::class,
                [
                    'data' => $objects,
                    'language' => Yii::t('app', 'ru'),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Выберите объект..'),
                        'style' => ['height' => '42px', 'padding-top' => '10px']
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
        }
    }
    echo '<br/>';
    echo '<label id="error" style="color: red"></label>';

    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    $(document).one("beforeSubmit", "#addAttributeForm", function (e) {
        e.preventDefault();
    }).one('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#addAttributeForm').serialize(),
            url: "../attribute/save",
            success: function (code) {
                let message = JSON.parse(code);
                if (message.code === 0) {
                    $('#modalAddAttribute').modal('hide');
                    let ajax = document.getElementById('modalParameterContent');
                    ajax.value = true
                } else {
                    let div = document.getElementById('error');
                    div.innerHTML = message.message;
                }
            },
            error: function (message) {
                $('#error').val(message.message);
            }
        });
    });
</script>
<?php ActiveForm::end(); ?>
