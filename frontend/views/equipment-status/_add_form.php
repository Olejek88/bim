<?php
/* @var $model */

use common\components\MainFunctions;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'addStatusForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить статус оборудования') ?></h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($model, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);
    $form->field($model, 'title')->textInput(['maxlength' => true]);
    echo '<br/><label id="error" style="color: red"></label>';
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Добавить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default"
            data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    var send = false;
    $(document).on("beforeSubmit", "#addStatusForm", function (e) {
        e.preventDefault();
    }).on('submit', "#addStatusForm", function (e) {
        e.preventDefault();
        if (!send) {
            send = true;
            $.ajax({
                type: "post",
                data: $('#addStatusForm').serialize(),
                url: "../equipment-status/save",
                success: function (code) {
                    let message = JSON.parse(code);
                    if (message.code === 0) {
                        send = true;
                        $('#addStatusForm').modal('hide');
                    } else {
                        let div = document.getElementById('error');
                        div.innerHTML = message.message;
                    }
                },
                error: function (message) {
                    $('#error').val(message.message);
                }
            });
        }
    });
</script>
<?php ActiveForm::end(); ?>
