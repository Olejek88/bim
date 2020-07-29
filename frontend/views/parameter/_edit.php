<?php
/* @var $parameter Parameter */
/* @var $entityUuid */
/* @var $parameterTypeUuid */

/* @var $date */

use common\components\MainFunctions;
use common\models\Parameter;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'addParameterForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить/редактировать параметр') ?></h4>
</div>
<div class="modal-body">
    <?php
    if (!$parameter->isNewRecord) {
        echo $form->field($parameter, 'uuid')->hiddenInput(['value' => $parameter['uuid']])->label(false);
        echo Html::hiddenInput("parameterUuid", $parameter['uuid']);
    } else {
        echo $form->field($parameter, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    echo $form->field($parameter, 'parameterTypeUuid')->hiddenInput(['value' => $parameterTypeUuid])->label(false);
    echo $form->field($parameter, 'entityUuid')->hiddenInput(['value' => $entityUuid])->label(false);
    echo $form->field($parameter, 'value')->textInput(['maxlength' => true]);
    echo $form->field($parameter, 'date')->hiddenInput(['value' => $date])->label(false);
    echo '<br/>';
    echo '<label id="error" style="color: red"></label>';

    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    $(document).on("beforeSubmit", "#addParameterForm", function (e) {
        e.preventDefault();
    }).one('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#addParameterForm').serialize(),
            url: "../parameter/save",
            success: function (code) {
                let message = JSON.parse(code);
                if (message.code === 0) {
                    $('#modalEditParameter').modal('hide');
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
