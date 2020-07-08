<?php
/* @var $measure Measure */
/* @var $measureChannelUuid string */
/* @var MeasureChannel[] $channels */

use common\components\MainFunctions;
use common\models\Measure;
use common\models\MeasureChannel;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'addMeasureForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Событие') ?></h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($measure, 'uuid')->hiddenInput(['value' => MainFunctions::GUID()])->label(false);

    if ($measure['measureChannelUuid'] != null) {
        echo $form->field($measure, 'measureChannelUuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else {
        if ($measureChannelUuid != null) {
            echo $form->field($measure, 'measureChannelUuid')->hiddenInput(['value' => $measureChannelUuid])->label(false);
        } else {
            echo $form->field($measure, 'measureChannelUuid')->widget(Select2::class,
                [
                    'data' => $channels,
                    'language' => Yii::t('app', 'ru'),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Выберите канал..'),
                        'style' => ['height' => '42px', 'padding-top' => '10px']
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
        }
    }
    echo $form->field($measure, 'date')->hiddenInput(['value' => date("Ymdhis")])->label(false);
    echo $form->field($measure, 'value')->textInput(['maxlength' => true]);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    $(document).on("beforeSubmit", "#addMeasureForm", function (e) {
        e.preventDefault();
    }).one('submit', "#addMeasureForm", function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#addMeasureForm').serialize(),
            url: "../measure/save",
            success: function (code) {
                let message = JSON.parse(code);
                if (message.code === 0) {
                    send = true;
                    $('#addMeasureForm').modal('hide');
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
