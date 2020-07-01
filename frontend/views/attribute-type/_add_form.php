<?php
/* @var $model AttributeType */

use common\components\MainFunctions;
use common\models\AttributeType;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    //'action' => '../attribute-type/save',
    'options' => [
        'id' => 'attributeTypeForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Тип атрибута') ?></h4>
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
    $(document).on("beforeSubmit", "#attributeTypeForm", function (e) {
        e.preventDefault();
    }).one('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#attributeTypeForm').serialize(),
            url: "../attribute-type/save",
            success: function (code) {
                let message = JSON.parse(code);
                if (message.code === 0) {
                    send = true;
                    $('#attributeTypeForm').modal('hide');
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
