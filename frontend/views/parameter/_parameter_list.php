<?php
/* @var $parameters common\models\Parameter */

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center"><?php echo Yii::t('app', 'Параметры') ?></h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th><?php echo Yii::t('app', 'Время') ?></th>
            <th><?php echo Yii::t('app', 'Тип параметра') ?></th>
            <th><?php echo Yii::t('app', 'Значение') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($parameters as $parameter): ?>
            <tr>
                <td><?= $parameter['date'] ?></td>
                <td><?= $parameter['parameterType']->title ?></td>
                <td><?= $parameter['value'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
