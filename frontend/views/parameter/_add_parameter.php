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
    'action' => '../parameter/add',
    'options' => [
        'id' => 'formAddParameter',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить атрибут') ?></h4>
</div>
<div class="modal-body">
    <?php
    if (!$model->isNewRecord) {
        echo $form->field($parameter, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($parameter, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }
    echo $form->field($parameter, 'parameterTypeUuid')->widget(Select2::class,
        [
            'name' => 'kv_types',
            'language' => Yii::t('app', 'ru'),
            'data' => $items,
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип атрибута ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    echo $form->field($parameter, 'entityUuid')->hiddenInput(['value' => $entityUuid])->label(false);
    echo $form->field($parameter, 'value')->textInput(['maxlength' => true]);
    echo $form->field($parameter, 'date')
        ->hiddenInput(['value' => date("Ymd")])
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
        $.ajax({
            type: "post",
            data: $('#formAddParameter').serialize(),
            url: "../parameter/add",
            success: function () {
                $('#modalAdd').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
