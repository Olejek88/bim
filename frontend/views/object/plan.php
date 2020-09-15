<?php
/* @var $objects
 * @var $dates
 * @var $month_count
 */

$this->title = Yii::t('app', 'ПолиТЭР::Календарь целей');

$this->registerJs('$("#modalEditParameter").on("hidden.bs.modal",
function () {
    $(this).removeData();
     window.location.replace("plan");
})');

?>
<div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
        <thead>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" colspan="25">Календарь целей потребления тепла объектов</th>
        </tr>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0"
                style="width: 200px;" rowspan="2">Объект
            </th>
            <?php
            foreach ($dates as $date) {
                echo '<th class="text-center kv-align-middle" colspan="2">' . $date . '</th >';
            }
            ?>
        </tr>
        <tr>
            <?php
            foreach ($dates as $date) {
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
            foreach ($object['plans'] as $plan) {
                echo '<td class="text-center kv-align-center kv-align-middle" style="background-color: #e0e0e0">' . $plan['plan'] . '</td>';
                echo '<td class="text-center kv-align-center kv-align-middle">' . $plan['fact'] . '</td>';
            }
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<div class="modal remote fade" id="modalEditParameter">
    <div class="modal-dialog" style="width: 950px">
        <div class="modal-content loader-lg" style="margin: 10px; padding: 10px">
        </div>
    </div>
</div>
