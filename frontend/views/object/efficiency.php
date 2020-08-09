<?php
/* @var $objects
 * @var $year
 * @var $month_count
 */

$this->title = Yii::t('app', 'ПолиТЭР::Сводная таблица по энергоэффективности');

$this->registerJs('$("#modalEditParameter").on("hidden.bs.modal",
function () {
    $(this).removeData();    
})');

use yii\helpers\Html; ?>
<div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
        <thead>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" colspan="26">
                Сводная таблица по энергоэффективности
            </th>
        </tr>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Объект</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Район</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1" colspan="3">Базовое потребление</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Площадь</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Толщина стен</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1" colspan="3">К-нт теплопроводности</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Объем</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Этажность</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Человек</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1" colspan="3">Удельное потребление</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1" colspan="3">Энергоэффективность</th>
        </tr>
        <tr>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1">Воды</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1">Тепла</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1">Энергии</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1">Стен</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1">Крыши</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1">Окон</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[0] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[1] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[2] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[0] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[1] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[2] ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($objects as $object) {
            echo '<tr data-key="1">';
            echo '<td class="kv-align-middle">' . $object['title'] . '</td>';
            if ($object['region'])
                echo '<td class="kv-align-middle">' . Html::a($object['region']['title'], ['object/efficiency', 'district' => $object['region']['uuid']]) . '</td>';
            else
                echo '<td class="kv-align-middle"></td>';
            echo '<td class="kv-align-middle text-center">' . $object['base_heat'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['base_water'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['base_energy'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['square'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['wall_width'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['cnt_wall'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['cnt_roof'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['cnt_window'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['volume'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['stage'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['workers'] . '</td>';

            echo '<td class="kv-align-middle text-center">' . $object['consumption'][0] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['consumption'][1] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['consumption'][2] . '</td>';

            echo '<td class="kv-align-middle text-center">' . $object['efficiency'][0] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['efficiency'][1] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['efficiency'][2] . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<div class="modal remote fade" id="modalEditParameter">
    <div class="modal-dialog" style="width: 450px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
