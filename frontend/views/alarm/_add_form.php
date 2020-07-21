<?php
/* @var $alarm Alarm */
/* @var $objectUuid */

/* @var Objects[] $objects */

use common\components\MainFunctions;
use common\models\Alarm;
use common\models\Objects;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute('/alarm/validation'),
    'action' => '../alarm/save',
    'options' => [
        'id' => 'addAlarmForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Событие') ?></h4>
</div>
<div class="modal-body">
    <?php
    if (!$alarm->isNewRecord) {
        echo $form->field($alarm, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($alarm, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }

    echo $form->field($alarm, 'title')->textInput(['maxlength' => true]);
    echo $form->field($alarm, 'level')->widget(Select2::class,
        [
            'data' => array(
                Alarm::LEVEL_FIXED => 'Решена',
                Alarm::LEVEL_INFO => 'Информация',
                Alarm::LEVEL_WARNING => 'Предупреждение',
                Alarm::LEVEL_PROBLEM => 'Проблема',
                Alarm::LEVEL_ERROR => 'Тревога',
                Alarm::LEVEL_CRITICAL => 'Критично'
            ),
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите уровень..'),
                'style' => ['height' => '42px', 'padding-top' => '10px']
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    if ($alarm['entityUuid'] != null) {
        echo $form->field($alarm, 'entityUuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else {
        if ($objectUuid) {
            echo $form->field($alarm, 'entityUuid')->hiddenInput(['value' => $objectUuid])->label(false);
        } else {
            echo $form->field($alarm, 'entityUuid')->widget(Select2::class,
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
    $(document).on("beforeSubmit", "#addAlarmForm", function (e) {
        e.preventDefault();
    }).one('submit', "#addAlarmForm", function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#addAlarmForm').serialize(),
            url: "../alarm/save",
            success: function (code) {
                let message = JSON.parse(code);
                if (message.code === 0) {
                    $('#modalAddAlarm').modal('hide');
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
