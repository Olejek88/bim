<?php
/* @var $model ParameterType */

use common\components\MainFunctions;
use common\models\ParameterType;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'addParameterTypeForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Тип параметра') ?></h4>
</div>
<div class="modal-body">
    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($model, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    ?>
    <?= $form->field($model, 'title')->textInput() ?>
    <?php echo '<label id="error" style="color: red"></label>'; ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    var send = false;
    $(document).on("beforeSubmit", "#addParameterTypeForm", function (e) {
        e.preventDefault();
    }).on('submit', "#addParameterTypeForm", function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#addParameterTypeForm').serialize(),
            url: "../parameter-type/save",
            success: function (code) {
                let message = JSON.parse(code);
                if (message.code === 0) {
                    send = true;
                    $('#addParameterTypeForm').modal('hide');
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
