<?php
/* @var $event Event */

/* @var $plan */

use common\models\Event;
use kartik\date\DatePicker;
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
    echo Html::hiddenInput("eventUuid", $event['uuid']);
    /*    echo $form->field($event, '_id')->hiddenInput(['value' => $event->_id])->label(false);
        echo $form->field($event, 'uuid')->hiddenInput(['value' => $event->uuid])->label(false);
        echo $form->field($event, 'title')->hiddenInput(['value' => $event->title])->label(false);
        echo $form->field($event, 'objectUuid')->hiddenInput(['value' => $event['objectUuid']])->label(false);
        echo $form->field($event, 'description')->hiddenInput(['value' => $event['description']])->label(false);*/
    if ($plan) {
        echo DatePicker::widget(
            [
                'model' => $event,
                'attribute' => 'date',
                'value' => date("Ymdhis"),
                'language' => Yii::t('app', 'ru'),
                'size' => 'ms',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ]
            ]
        );
        echo $form->field($event, 'dateFact')->hiddenInput(['value' => $event['dateFact']])->label(false);
    } else {
        echo DatePicker::widget(
            [
                'model' => $event,
                'attribute' => 'dateFact',
                'value' => date("Ymdhis"),
                'language' => Yii::t('app', 'ru'),
                'size' => 'ms',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ]
            ]
        );
        echo $form->field($event, 'date')->hiddenInput(['value' => $event['date']])->label(false);
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
    var send = false;
    $(document).on("beforeSubmit", "#addEventForm", function (e) {
        e.preventDefault();
    }).on('submit', "#addEventForm", function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#addEventForm').serialize(),
            url: "../event/change",
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
