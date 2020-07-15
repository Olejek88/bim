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

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'addEventForm',
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

    if ($alarm['objectUuid'] != null) {
        echo $form->field($alarm, 'objectUuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else {
        if ($objectUuid) {
            echo $form->field($alarm, 'objectUuid')->hiddenInput(['value' => $objectUuid])->label(false);
        } else {
            echo $form->field($alarm, 'objectUuid')->widget(Select2::class,
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
    echo $form->field($alarm, 'description')->textInput(['maxlength' => true]);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    var send = false;
    $(document).on("beforeSubmit", "#addEventForm", function (e) {
        e.preventDefault();
    }).on('submit', "#addEventForm", function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#addEventForm').serialize(),
            url: "../event/save",
            success: function (code) {
                let message = JSON.parse(code);
                if (message.code === 0) {
                    send = true;
                    $('#addEventForm').modal('hide');
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
