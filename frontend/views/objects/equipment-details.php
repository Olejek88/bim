<?php

use common\models\Defect;
use common\models\Equipment;
use Da\QrCode\QrCode;
use yii\helpers\Html;

//use Mpdf\QrCode\QrCode;

/* @var $model Equipment */
/* @var $equipmentModels */
/* @var $users */

$defects = Defect::find()
    ->where(['equipmentUuid' => $model['uuid']])
    ->orderBy('createdAt desc')
    ->limit(5)
    ->all();

?>
<div id="<?= $model['_id'] ?>" class="kv-expand-row kv-grid-demo">
    <div class="kv-detail-content">
        <h3><?php echo $equipmentModels[$model['equipmentModelUuid']]['title'] ?>
            <small><?php echo $model['title'] ?></small>
        </h3>
        <div class="row">
            <div class="col-sm-2">
                <div class="img-thumbnail img-rounded text-center">
                    <img src="<?php echo Html::encode(Equipment::getImageUrlStatic($model['equipmentModelUuid'], $model['image'], $equipmentModels[$model['equipmentModelUuid']]['image'])) ?>"
                         style="padding:2px;width:100%">
                    <div class="small text-muted"><?php echo $model['inventoryNumber'] ?></div>
                </div>
            </div>
            <div class="col-sm-4">
                <table class="table table-bordered table-condensed table-hover small kv-table">
                    <tr class="success">
                        <th colspan="2" class="text-center text-danger">
                            <?php echo Yii::t('app', 'Параметры оборудования') ?></th>
                    </tr>
                    <tr>
                        <td><?php echo Yii::t('app', 'UUID') ?>
                        </td>
                        <td class="text-right">
                            <?php echo $model['uuid'] ?></td>
                    </tr>
                    <tr>
                        <td><?php echo Yii::t('app', 'Идентификатор') ?>
                        </td>
                        <td class="text-right">
                            <?php echo $model['tagId'] ?></td>
                    </tr>
                    <tr>
                        <td><?php echo Yii::t('app', 'Инвентарный номер') ?>
                        </td>
                        <td class="text-right">
                            <?php echo $model['inventoryNumber'] ?></td>
                    </tr>
                    <tr>
                        <td><?php echo Yii::t('app', 'Серийный номер') ?>
                        </td>
                        <td class="text-right">
                            <?php echo $model['serialNumber'] ?></td>
                    </tr>
                    <tr>
                        <td><?php echo Yii::t('app', 'Дата ввода в эксплуатацию') ?></td>
                        <td class="text-right"><?php echo $model['startDate'] ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-3">
                <table class="table table-bordered table-condensed table-hover small kv-table">
                    <tr class="danger">
                        <th colspan="4" class="text-center text-success"><?php echo Yii::t('app', 'Дефекты') ?>
                            <?php
                            echo Html::a('<span class="fa fa-plus"></span>',
                                ['../defect/add',
                                    'uuid' => $model['uuid'],
                                    'source' => '../equipment/index'
                                ],
                                [
                                    'title' => Yii::t('app', 'Добавить дефект'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalAddDefect',
                                ]);
                            ?>
                        </th>
                    </tr>
                    <tr class="active">
                        <th class="text-center">#</th>
                        <th><?php echo Yii::t('app', 'Пользователь') ?></th>
                        <th><?php echo Yii::t('app', 'Тип дефекта') ?></th>
                        <th class="text-right"><?php echo Yii::t('app', 'Дата') ?></th>
                    </tr>
                    <?php
                    foreach ($defects as $defect) {
                        echo '<tr>
                                  <td class="text-center">' . $defect['_id'] . '</td>
                                  <td>' . $users[$defect['userUuid']] . '</td>
                                  <td>' . $defect['comment'] . '</td>
                                  <td class="text-right">' . $defect['date'] . '</td>
                                  </tr>';
                    }
                    ?>
                </table>
            </div>
            <div class="col-sm-2">
                <?php
                if (isset($model['tagId'])) {
                    $url = Equipment::getImageQrStatic($model['uuid'], $model['tagId']);
                    if ($url !== false) {
                        echo "<img src=" .
                            Html::encode(Equipment::getImageQrStatic($model['uuid'], $model['tagId'])) .
                            " style='padding:2px;width:100%'>";
                    } else {
                        // Если картинка QR потерялась, то пусть рендерится
                        $qrCode = (new QrCode($model['tagId']))
                            ->setSize(200)
                            ->setMargin(5)
                            ->useForegroundColor(0, 0, 0);
                        if ($qrCode) {
                            /** @var $exception */
                            try {
                                echo '<img src="' . $qrCode->writeDataUri() . '"/>';
                            } catch (Exception $e) {
                                Yii::error($e->getMessage(), 'backend/views/equipment/equipment-details.php');
                            }
                        }
                    }
                }
                ?>
            </div>
            <div class="col-sm-1">
                <div class="kv-button-stack">
                    <?php
                    /*                            echo Html::a('<span class="glyphicon glyphicon-book"></span>',
                                                    ['/documentation','uuid' => $model['uuid']], ['class'=>'btn btn-default btn-lg',
                                                        'type' => 'button', 'title' => 'Документация', 'data-toggle' => 'tooltip']);*/
                    ?>
                    <?php
                    /*                            echo Html::a('<span class="glyphicon glyphicon-list"></span>',
                                                    ['/equipment-register','uuid' => $model['uuid']], ['class'=>'btn btn-default btn-lg',
                                                        'type' => 'button', 'title' => 'Журнал', 'data-toggle' => 'tooltip']);*/
                    ?>
                </div>
                <div class="kv-button-stack">
                    <?php
                    /*                            echo Html::a('<span class="glyphicon glyphicon-list-alt"></span>',
                                                    ['/equipment-stage/tree','typeUuid' => $model['equipmentModel']['equipmentTypeUuid']],
                                                    ['class'=>'btn btn-default btn-lg', 'type' => 'button', 'title' => 'Задачи', 'data-toggle' => 'tooltip']);*/
                    ?>
                    <?php
                    /*                            echo Html::a('<span class="glyphicon glyphicon-picture"></span>',
                                                    ['/equipment-attribute','uuid' => $model['uuid']], ['class'=>'btn btn-default btn-lg',
                                                        'type' => 'button', 'title' => 'Аттрибуты', 'data-toggle' => 'tooltip']);*/
                    ?>
                </div>

            </div>
        </div>
    </div>
</div>
