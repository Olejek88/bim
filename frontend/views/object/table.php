<?php
/* @var $objects
 * @var $dates
 * @var $month_count
 */

$this->title = Yii::t('app', 'ПолиТЭР::Гипертаблица объектов');
?>
<div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
        <thead>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" colspan="25">Сводная таблица объектов</th>
        </tr>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Объект</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Район</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Тип объекта</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Класс ЭЭ</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Показатель ЭО</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Учет тепла</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Учет воды</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Учет ЭЭ</th>
            <?php
            foreach ($dates as $date) {
                echo '<th class="text-center kv-align-middle" colspan="2">Тепло ' . $date . '</th >';
                echo '<th class="text-center kv-align-middle" colspan="2">Вода ' . $date . '</th >';
                echo '<th class="text-center kv-align-middle" colspan="2">ЭЭ ' . $date . '</th >';
            }
            ?>
        </tr>
        <tr>
            <?php
            foreach ($dates as $date) {
                echo '<th class="text-center kv-align-middle" style="background-color: #e0e0e0">План</th >';
                echo '<th class="text-center kv-align-middle">Факт</th >';
                echo '<th class="text-center kv-align-middle" style="background-color: #e0e0e0">План</th >';
                echo '<th class="text-center kv-align-middle">Факт</th >';
                echo '<th class="text-center kv-align-middle" style="background-color: #e0e0e0">План</th >';
                echo '<th class="text-center kv-align-middle">Факт</th >';
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($objects as $object) {
            echo '<tr data-key="1">';
            echo '<td class="kv-align-middle">' . $object['title'] . '</td>';
            if ($object['region'])
                echo '<td class="kv-align-middle">' . $object['region']['title'] . '</td>';
            else
                echo '<td class="kv-align-middle">-</td>';
            echo '<td class="text-center kv-align-middle">' . $object['type'] . '</td>';
            echo '<td class="text-center kv-align-middle">' . $object['efficiency'] . '</td>';
            echo '<td class="text-center kv-align-middle">' . $object['equipment'] . '</td>';

            echo '<td class="text-center kv-align-middle">' . $object['heat'] . '</td>';
            echo '<td class="text-center kv-align-middle">' . $object['water'] . '</td>';
            echo '<td class="text-center kv-align-middle">' . $object['electricity'] . '</td>';

            foreach ($object['plans'] as $plan) {
                echo '<td class="text-center kv-align-center kv-align-middle" style="background-color: #e0e0e0">' . $plan['plan_heat'] . '</td>';
                echo '<td class="text-center kv-align-center kv-align-middle">' . $plan['fact_heat'] . '</td>';
                echo '<td class="text-center kv-align-center kv-align-middle" style="background-color: #e0e0e0">' . $plan['plan_water'] . '</td>';
                echo '<td class="text-center kv-align-center kv-align-middle">' . $plan['fact_water'] . '</td>';
                echo '<td class="text-center kv-align-center kv-align-middle" style="background-color: #e0e0e0">' . $plan['plan_electricity'] . '</td>';
                echo '<td class="text-center kv-align-center kv-align-middle">' . $plan['fact_electricity'] . '</td>';
            }
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>
