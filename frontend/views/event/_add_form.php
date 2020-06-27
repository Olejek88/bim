<?php
/* @var $event Event */

/* @var Objects[] $objects */

use common\models\Event;
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
    echo $form->field($event, 'title')->textInput(['maxlength' => true]);

    if ($model['objectUuid'] != null) {
        echo $form->field($event, 'objectUuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else {
        echo $form->field($event, 'objectUuid')->widget(Select2::class,
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
    echo $form->field($event, 'description')->textInput(['maxlength' => true]);
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
