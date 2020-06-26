<?php

/* @var $events */
/* @var $type integer */
/* @var $today_date */

/* @var $id integer */

use kartik\select2\Select2;
use yii\helpers\Html;

$this->title = Yii::t('app', 'ПолиТЭР::Лента событий пользователя');
//$type = htmlspecialchars(Yii::$app->request->getQueryParam('type', 0));
//$id = htmlspecialchars(Yii::$app->request->getQueryParam('id'));
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?php echo Yii::t('app', 'Лента действий пользователя') ?>
            <small><?php echo Yii::t('app', 'зарегистрированные дефекты, выполненные наряды, изменения параметров') ?></small>
        </h1>
        <form action="/users/timeline?id=<?= $id ?>">
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
                                    Yii::t('app', 'Наряды'),
                                    Yii::t('app', 'Дефекты'),
                                    Yii::t('app', 'Параметры')
                                ],
                                'options' => ['placeholder' => Yii::t('app', 'Тип события')],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) .
                            Html::hiddenInput('id', $id)
                            . '</td><td>&nbsp;</td><td style="width: 100px">' .
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
