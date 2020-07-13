<?php
/* @var $object Objects */
/* @var $objects Objects [] */
/* @var $latlng */
/* @var $objectSubTypeUuid */

/* @var $objectTypeUuid */

use common\components\MainFunctions;
use common\models\Objects;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'form-object',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить район') ?></h4>
</div>
<div class="modal-body">
    <?php
    $latDefault = 55.160374;
    $lngDefault = 61.402738;
    echo $form->field($object, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);
    echo $form->field($object, 'title')->textInput(['maxlength' => true]);
    echo $form->field($object, 'deleted')->hiddenInput(['value' => 0])->label(false);

    echo $form->field($object, 'parentUuid')->widget(Select2::class,
        [
            'data' => $objects,
            'language' => Yii::t('app', 'ru'),
            'options' => [
                'placeholder' => Yii::t('app', 'Родитель..')
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo $form->field($object, 'objectTypeUuid')->hiddenInput(['value' => $objectTypeUuid])->label(false);
    echo $form->field($object, 'objectSubTypeUuid')->hiddenInput(['value' => $objectSubTypeUuid])->label(false);
    echo $form->field($object, 'fiasGuid')->hiddenInput(['value' => "-"])->label(false);
    echo $form->field($object, 'fiasParentGuid')->hiddenInput(['value' => "-"])->label(false);
    echo $form->field($object, 'okato')->hiddenInput(['value' => "-"])->label(false);
    echo Html::hiddenInput("latlng", $latlng);

    echo $form->field($object, 'latitude')->hiddenInput(['maxlength' => true, 'value' => $latDefault])->label(false);
    echo $form->field($object, 'longitude')->hiddenInput(['maxlength' => true, 'value' => $lngDefault])->label(false);
    ?>

</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    var send = false;
    $(document).on("beforeSubmit", "#form-object", function (e) {
        e.preventDefault();
    }).on('submit', '#form-object', function (e) {
        e.preventDefault();
        if (!send) {
            send = true;
            $.ajax({
                type: "post",
                data: $('#form-object').serialize(),
                url: "../object/save-district",
                success: function () {
                    $('#modalAdd').modal('hide');
                },
                error: function () {
                }
            });
        }
    });
</script>
<?php ActiveForm::end(); ?>
