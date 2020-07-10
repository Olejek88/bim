<?php
/* @var $parameter Parameter */
/* @var $parameterTypes ParameterType[] */

/* @var $entityUuid */

use common\components\MainFunctions;
use common\models\Parameter;
use common\models\ParameterType;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'formAddParameter',
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
    echo $form->field($parameter, 'date')
        ->hiddenInput(['value' => date("Ymdhis")])
        ->label(false);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    $(document).on("beforeSubmit", "#formAddParameter", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#formAddParameter').serialize(),
            url: "../parameter/save",
            success: function () {
                $('#modalAddParameter').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
