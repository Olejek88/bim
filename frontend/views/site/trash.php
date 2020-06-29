<?php

/* @var $events */
/* @var $type integer */

/* @var $today_date */

use kartik\select2\Select2;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ПолиТЭР::Корзина объектов');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <form action="">
            <table style="width: 400px; padding: 3px">
                <tr>
                    <td style="width: 300px">
                        <?php
                        echo Select2::widget([
                                'id' => 'type',
                                'name' => 'type',
                                'value' => $type,
                                'language' => Yii::t('app', 'ru'),
                                'data' => [
                                    Yii::t('app', 'Все объекты'),
                                    Yii::t('app', 'Объекты'),
                                    Yii::t('app', 'Каналы измерения')],
                                'options' => ['placeholder' => Yii::t('app', 'Тип')],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) . '</td><td>&nbsp;</td><td style="width: 100px">' .
                            Html::submitButton(Yii::t('app', 'Выбрать'),
                                ['class' => 'btn btn-success']) . '';
                        ?>
                    </td>
                </tr>
            </table>
        </form>
        <h1>
            <?php echo Yii::t('app', 'Корзина удаленных объектов') ?>
            <small><?php echo Yii::t('app', 'для восстановления требуется нажать на иконку восстановления') ?></small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="timeline">
                    <li class="time-label">
                        <span class="bg-blue">
                             <?php echo $today_date ?>
                        </span>
                    </li>
                    <?php
                    if (count($events)) {
                        $date = $events[0]['date'];
                        foreach ($events as $event) {
                            echo $event['event'];
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </section>
</div>
