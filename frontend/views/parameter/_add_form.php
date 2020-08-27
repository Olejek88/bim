<?php
/* @var $parameter Parameter */
/* @var $parameterTypes ParameterType[] */

/* @var $entityUuid */

use common\components\MainFunctions;
use common\models\Parameter;
use common\models\ParameterType;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute('/parameter/validation'),
    'action' => '../parameter/save',
    'options' => [
        'id' => 'addParameterForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить параметр') ?></h4>
</div>
<div class="modal-body">
    <?php
    if (!$parameter->isNewRecord) {
        echo $form->field($parameter, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($parameter, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    echo $form->field($parameter, 'parameterTypeUuid')->widget(Select2::class,
        [
            'name' => 'kv_types',
            'language' => Yii::t('app', 'ru'),
            'data' => $parameterTypes,
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип параметра ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    echo $form->field($parameter, 'entityUuid')->hiddenInput(['value' => $entityUuid])->label(false);
    echo $form->field($parameter, 'value')->textInput(['maxlength' => true]);
    echo Html::hiddenInput("objectUuid", $entityUuid);
    echo $form->field($parameter, 'date')
        ->hiddenInput(['value' => date("Ymdhis")])
        ->label(false);
    ?>
    <div class="pole-mg">
        <p style="width: 300px; margin-bottom: 0;"><?php echo Yii::t('app', 'Дата актуализации параметра') ?></p>
        <?php echo DatePicker::widget(
            [
                'model' => $parameter,
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
        ?>
    </div>
    <?php
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
                    $('#modalAddParameter').modal('hide');
                    $('#modalPlan').modal('hide');
                    let ajax = document.getElementById('modalPlan');
                    ajax.value = true
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
