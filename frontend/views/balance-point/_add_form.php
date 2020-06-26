<?php
/* @var $model */
/* @var MeasureChannel[] $measureChannels */

/* @var Objects[] $objects */

use common\components\MainFunctions;
use common\models\BalancePoint;
use common\models\MeasureChannel;
use common\models\Objects;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'addPointForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить точку баланса') ?></h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($model, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);

    echo $form->field($model, 'measureChannelUuid')->widget(Select2::class,
        [
            'data' => $measureChannels,
            'language' => Yii::t('app', 'ru'),
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите канал..'),
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo $form->field($model, 'objectUuid')->widget(Select2::class,
        [
            'data' => $objects,
            'language' => Yii::t('app', 'ru'),
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите объект..'),
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo $form->field($model, 'userUuid')->widget(Select2::class,
        [
            'data' => [
                BalancePoint::CONSUMER,
                BalancePoint::INPUT,
                BalancePoint::OUTPUT],
            'language' => Yii::t('app', 'ru'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    echo '<label id="error" style="color: red"></label>';
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Добавить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default"
            data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    var send = false;
    $(document).on("beforeSubmit", "#addPointForm", function (e) {
        e.preventDefault();
    }).on('submit', "#addPointForm", function (e) {
        e.preventDefault();
        if (!send) {
            send = true;
            $.ajax({
                type: "post",
                data: $('#addPointForm').serialize(),
                url: "../balance-point/save",
                success: function (code) {
                    let message = JSON.parse(code);
                    if (message.code === 0) {
                        send = true;
                        $('#addPointForm').modal('hide');
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
