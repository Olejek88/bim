<?php
/* @var $event Event */
/* @var $objectUuid */

/* @var $objects */

/* @var $types */

use common\components\MainFunctions;
use common\models\Event;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'addEventForm',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Событие') ?></h4>
</div>
<div class="modal-body">
    <?php
    if (!$event->isNewRecord) {
        echo $form->field($event, 'uuid')->textInput(['maxlength' => true, 'readonly' => true]);
    } else {
        echo $form->field($event, 'uuid')->hiddenInput(['value' => (new MainFunctions)->GUID()])->label(false);
    }

    echo $form->field($event, 'title')->textInput(['maxlength' => true]);

    if ($event['objectUuid'] != null) {
        echo $form->field($event, 'objectUuid')->hiddenInput(['value' => $model['uuid']])->label(false);
    } else {
        if ($objectUuid) {
            echo $form->field($event, 'objectUuid')->hiddenInput(['value' => $objectUuid])->label(false);
        } else {
            echo $form->field($event, 'objectUuid')->widget(Select2::class,
                [
                    'data' => $objects,
                    'language' => Yii::t('app', 'ru'),
                    'options' => [
                        'placeholder' => Yii::t('app', 'Выберите объект..'),
                        'style' => ['height' => '42px', 'padding-top' => '10px']
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
        }
    }
    echo $form->field($event, 'eventTypeUuid')->widget(Select2::class,
        [
            'data' => $types,
            'language' => Yii::t('app', 'ru'),
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите тип события..'),
                'style' => ['height' => '42px', 'padding-top' => '10px']
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
    echo $form->field($event, 'description')->textInput(['maxlength' => true]);
    echo '<p style="width: 300px; margin-bottom: 0; font-weight: bold">Дата назначения</p>';
    echo DatePicker::widget(
        [
            'model' => $event,
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
    echo '<br/>';
    echo $form->field($event, 'cnt_coverage')->textInput();
    echo '<br/>';
    echo '<label id="error" style="color: red"></label>';
    ?>
</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    var send = false;
    $(document).on("beforeSubmit", "#addEventForm", function (e) {
        e.preventDefault();
    }).on('submit', "#addEventForm", function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: $('#addEventForm').serialize(),
            url: "../event/save",
            success: function (code) {
                let message = JSON.parse(code);
                if (message.code === 0) {
                    send = true;
                    $('#addEventForm').modal('hide');
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
