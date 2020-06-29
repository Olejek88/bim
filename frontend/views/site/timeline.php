<?php

/* @var $events */
/* @var $today_date */

/* @var $type int */

use kartik\select2\Select2;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ПолиТЭР::Новости');
$type = htmlspecialchars(Yii::$app->request->getQueryParam('type', 0));
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo Yii::t('app', 'Лента событий') ?>
            <small><?php echo Yii::t('app', 'зарегистрированные дефекты, выполненные наряды, события системы') ?></small>
        </h1>
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
                                    Yii::t('app', 'Все события'),
                                    Yii::t('app', 'Объекты'),
                                    Yii::t('app', 'Параметры')
                                ],
                                'options' => ['placeholder' => Yii::t('app', 'Тип события')],
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
                            if ($event['date'] != $date) {
                                $date = $event['date'];
                                echo '<li class="time-label"><span class="bg-aqua btn-xs">' .
                                    date("d-m-Y", strtotime($date)) . '</span></li>';
                            }
                            echo $event['event'];
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </section>
</div>
