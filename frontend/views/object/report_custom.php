<?php
/* @var $data
 * @var $count
 */

$this->title = Yii::t('app', 'ПолиТЭР::Пользовательский отчет');
?>
<div id="requests-table-container" class="panel table-responsive kv-grid-container" style="overflow: auto">
    <table class="kv-grid-table table table-hover table-bordered table-condensed kv-table-wrap">
        <thead>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0" colspan="<?= $count ?>">
                Пользовательский отчет
            </th>
        </tr>
        <tr class="kartik-sheet-style" style="height: 20px">
            <th class="kv-align-middle" data-col-seq="0">Объект</th>
            <?php
            foreach ($data[0]['parameter_title'] as $title) {
                echo '<th class="text-center kv-align-middle">' . $title . '</th >';
            }
            ?>
            <?php
            foreach ($data[0]['measure_title'] as $title) {
                echo '<th class="text-center kv-align-middle">' . $title . '</th >';
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($data as $object) {
            echo '<tr data-key="1">';
            echo '<td class="kv-align-middle">' . $object['title'] . '</td>';
            foreach ($object['parameters'] as $parameter) {
                echo '<td class="text-center kv-align-center kv-align-middle">' . $parameter . '</td>';
            }
            foreach ($object['measures'] as $measure) {
                echo '<td class="text-center kv-align-center kv-align-middle">' . $measure . '</td>';
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
