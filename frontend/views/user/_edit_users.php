<?php

use common\models\User;
use frontend\models\Role;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $roleList */
/* @var $role Role */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'editUserForm',
        'enctype' => 'multipart/form-data'
    ]
]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Редактировать пользователя') ?></h4>
</div>
<div class="modal-body">
    <?php
    if (!$model->isNewRecord) {
        echo $form->field($model, 'id')->hiddenInput(['maxlength' => true, 'readonly' => true])->label(false);
    }
    ?>

    <?php echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php
    $items = [
        '10' => Yii::t('app', 'Активный'),
        '9' => Yii::t('app', 'Отключен'),
        '0' => Yii::t('app', 'Удален')];
    echo $form->field($model, 'status')->dropDownList($items);
    echo $form->field($model, 'pass')->passwordInput(['maxlength' => true, 'value' => '']);
    ?>

    <?php
    if (Yii::$app->user->can(User::PERMISSION_ADMIN)) {
        echo $form->field($role, 'role')
            ->label(Yii::t('app', 'Права пользователя в системе'))
            ->dropDownList($roleList);
    } else {
        echo $form->field($role, 'role')
            ->hiddenInput(['value' => User::ROLE_OPERATOR])
            ->label(false);
    }
    ?>

    <?php
    echo $form->field($model, 'image')->widget(
        FileInput::class,
        ['options' => ['accept' => '*'],]
    );
    ?>

    <div class="form-group text-center">
        <?= Html::submitButton(Yii::t('app', 'Принять'), [
            'class' => 'btn btn-success'
        ]) ?>
    </div>
</div>

<script>
    $(document).on("beforeSubmit", "#editUserForm", function () {
    }).one('submit', function (e) {
        e.preventDefault();
        let form = document.getElementById("editUserForm");
        let fd = new FormData(form);
        $.ajax({
            url: <?php
            if (!$model->isNewRecord) {
                echo '"../user/update?id=' . $model["id"] . '"';
            } else {
                echo '"../user/new"';
            }
            ?>,
            data: fd,
            processData: false,
            contentType: false,
            type: "post",
            success: function () {
                $('#modalUser').modal('hide');
            },
            error: function () {
            }
        })
    });
</script>

<?php ActiveForm::end(); ?>
