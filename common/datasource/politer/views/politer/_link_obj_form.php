<?php

/* @var $this View */
/* @var $types */

/* @var $objects Objects[] */

use common\models\Objects;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'formLinkObj',
        'enctype' => 'multipart/form-data'
    ]]);
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <?php
    $selectedItem = !empty($objects) ? array_key_first($objects) : '';
    echo Select2::widget([
        'data' => $objects,
        'language' => 'ru',
        'name' => 'objects',
        'value' => $selectedItem,
        'options' => [
            'placeholder' => 'Выберите объект для связи...',
        ],
        'pluginOptions' => [
//            'allowClear' => true,
        ],
    ]);
    echo Select2::widget(
        [
            'data' => $types,
            'name' => 'measureTypeUuid',
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип измерения ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo Select2::widget(
        [
            'data' => ['0' => 'Текущие', '1' => 'Часовой', '2' => 'Дневной', '4' => 'По месяцам', '7' => 'На дату'],
            'name' => 'type',
            'options' => ['placeholder' => Yii::t('app', 'Выберите тип ...')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo Html::textInput("errors", "", ['readonly' => 'readonly', 'style' => 'width:100%; color: red;', 'id' => 'errors', 'name' => 'errors'])

    ?>

</div>
<div class="modal-footer">
    <?php echo Html::submitButton('Отправить', ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>

<script>
    $(document)
        .off("submit", "#formLinkObj")
        .on("submit", "#formLinkObj", function (e) {
            e.preventDefault();
            let selectedObj = $("#extMeasureChannelsTree").data('selectedObj')
            let data = $("#formLinkObj").serialize() + '&path=' + selectedObj.path + '&id=' + selectedObj.id + '&folder=' + selectedObj.folder;
            console.log(data);
            let form = $(this);
            if (form.data('send') === true) {
                return;
            }

            $.ajax({
                type: "post",
                data: data,
                url: "link-obj-form",
                success: function () {
                    $("#modalLinkObj").modal('hide');
                },
                error: function (ret) {
                    $('#errors').val(ret.responseText);
                },
                complete: function () {
                    form.data('send', false);
                }
            });
        });
</script>
<?php ActiveForm::end(); ?>
