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

$this->registerJs('$("#modalParameters").on("hidden.bs.modal",
function () {
    $(this).removeData();    
})');

$this->registerJs('$("#modalEvents").on("hidden.bs.modal",
function () {
    $(this).removeData();    
})');

$this->registerCssFile('/css/site.css');

?>
<div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
        <thead>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" colspan="26">
                Прогноз по энергосбережению
            </th>
        </tr>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Объект</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Ссылки</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Площадь</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Этажей</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Базовое потребление</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1" colspan="3">Потребление</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1" colspan="3">События</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1" colspan="3">Прогноз</th>
        </tr>
        <tr>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[2] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[1] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[0] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[0] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[3] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[4] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[0] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[3] ?></th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="1"><?= $year[4] ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($objects as $object) {
            echo '<tr data-key="1">';
            echo '<td class="kv-align-middle">' . $object['title'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['links'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['square'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['stage'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['base_heat'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['consumption'][2] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['consumption'][1] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['consumption'][0] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['events'][0] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['events'][1] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['events'][2] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['prediction'][0] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['prediction'][1] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['prediction'][2] . '</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<div class="modal remote fade" id="modalEvents">
    <div class="modal-dialog" style="width: 1050px; height: 550px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalEditParameter">
    <div class="modal-dialog" style="width: 450px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
<div class="modal remote fade" id="modalParameters">
    <div class="modal-dialog" style="width: 1050px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
