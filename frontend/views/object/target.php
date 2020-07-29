<?php
/* @var $objects
 * @var $dates
 * @var $month_count
 */

$this->title = Yii::t('app', 'ПолиТЭР::Календарь целей');

$this->registerJs('$("#modalEditParameter").on("hidden.bs.modal",
function () {
    $(this).removeData();    
})');

?>
<div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
        <thead>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" colspan="26">
                Коэффициенты и базовое потребление объектов
                &nbsp;
                <a href="/object/target?type=heat"><span class="label label-info">Тепло</span></a>
                &nbsp;
                <a href="/object/target?type=water"><span class="label label-info">Вода</span></a>
                &nbsp;
                <a href="/object/target?type=energy"><span class="label label-info">Электроэнергия</span></a>
            </th>
        </tr>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Объект</th>
            <th class="kv-align-middle" data-col-seq="0" rowspan="2">Базовое потребление</th>
            <?php
            foreach ($dates as $date) {
                echo '<th class="text-center kv-align-middle" colspan="2">' . $date . '</th >';
            }
            ?>
        </tr>
        <tr>
            <?php
            foreach ($dates as $date) {
                echo '<th class="text-center kv-align-middle">К-нт</th >';
                echo '<th class="text-center kv-align-middle" style="background-color: #e0e0e0">Расчет</th >';
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($objects as $object) {
            echo '<tr data-key="1">';
            echo '<td class="kv-align-middle">' . $object['title'] . '</td>';
            echo '<td class="kv-align-middle text-center">' . $object['base'] . '</td>';
            foreach ($object['plans'] as $plan) {
                echo '<td class="text-center kv-align-center kv-align-middle">' . $plan['coefficient'] . '</td>';
                echo '<td class="text-center kv-align-center kv-align-middle" style="background-color: #e0e0e0">' . $plan['consumption'] . '</td>';
            }
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
