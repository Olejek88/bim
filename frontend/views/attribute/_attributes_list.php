<?php
/* @var $attributes common\models\Attribute */

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title text-center"><?php echo Yii::t('app', 'Атрибуты оборудования') ?></h4>
</div>
<div class="modal-body">
    <table class="table table-striped table-hover text-left">
        <thead>
        <tr>
            <th><?php echo Yii::t('app', 'Время') ?></th>
            <th><?php echo Yii::t('app', 'Тип аттибута') ?></th>
            <th><?php echo Yii::t('app', 'Параметр') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($attributes as $attribute): ?>
            <tr>
                <td><?= $attribute['date'] ?></td>
                <td><?= $attribute['attributeType']->name ?></td>
                <td><?= $attribute['value'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
