<?php
/* @var $equipmentAttribute */

use common\components\MainFunctions;
use common\models\AttributeType;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'action' => '../attribute/add',
    'options' => [
        'id' => 'form',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить атрибут') ?></h4>
</div>
<div class="modal-body">
    <?php
    echo $form->field($equipmentAttribute, 'uuid')
        ->hiddenInput(['value' => MainFunctions::GUID()])
        ->label(false);
    $attributeTypes = AttributeType::find()->all();
    $items = ArrayHelper::map($attributeTypes, 'uuid', 'name');
    echo $form->field($equipmentAttribute, 'attributeTypeUuid')->widget(Select2::class,
        [
            'name' => 'kv_types',
            'language' => Yii::t('app', 'ru'),
            'data' => $items,
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип атрибута ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label(false);
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    $(document).on("beforeSubmit", "#form", function (e) {
        e.preventDefault();
    }).on('submit', function (e) {
        $.ajax({
            type: "post",
            data: $('form').serialize(),
            url: "../attribute/add",
            success: function () {
                $('#modal_new').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>
<?php ActiveForm::end(); ?>
