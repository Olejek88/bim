<?php
/* @var $model EventType
 * @var $types
 */

use common\components\MainFunctions;
use common\models\EventType;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'attributeTypeForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Тип мероприятий') ?></h4>
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
    <?= $form->field($model, 'source')->widget(Select2::class,
        [
            'data' => array(
                '0' => 'Общий',
                '1' => 'Тепло',
                '2' => 'Вода',
                '3' => 'Электроэнергия'
            ),
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите тип..'),
                'style' => ['height' => '42px', 'padding-top' => '10px']
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    echo $form->field($model, 'parameterTypeUuid')->widget(Select2::class,
        [
            'data' => $types,
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип параметра ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    ?>
    <?= $form->field($model, 'cnt_effect')->textInput() ?>
    <?php echo '<label id="error" style="color: red"></label>'; ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    $(document).on("beforeSubmit", "#eventTypeForm", function (e) {
        e.preventDefault();
    }).one('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#eventTypeForm').serialize(),
            url: "../event-type/save",
            success: function (code) {
                let message = JSON.parse(code);
                if (message.code === 0) {
                    send = true;
                    $('#eventTypeForm').modal('hide');
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
