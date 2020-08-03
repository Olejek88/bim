<?php

/* @var $model Objects
 * @var $object
 */

use common\models\Objects;

$measures = [];
$categories = "[";
$values = "name: 'Значения', data: [";
$zero = 0;
$num = rand(0, 1000);
$counts = 0;
foreach ($measures as $measure) {
    if ($counts > 0) {
        $values .= ",";
        $categories .= ",";
    }
    $values .= $measure['value'];
    $categories .= "'" . date_format(date_create($measure['date']), 'Y-m-d') . "'";
    $counts++;
}
$values .= "]";
$categories .= "]";
?>
<div class="kv-expand-row kv-grid-demo">
    <div class="kv-expand-detail skip-export kv-grid-demo">
        <div class="skip-export kv-expanded-row kv-grid-demo" data-index="0" data-key="1">
            <div class="kv-detail-content">
                <h3><?php echo $object['deviceType']->title ?></h3>
                <div class="row">
                    <div class="col-sm-2">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="success">
                                <th colspan="2" class="text-center text-danger">Последние показания</th>
                            </tr>
                            <?php
                            foreach ($measures as $measure) {
                                echo '<tr><td>' . $measure['date'] . '</td>
                                          <td class="text-right">' . $measure['value'] . '</td></tr>';
                            }
                            ?>
                        </table>
                    </div>
                    <div class="col-sm-4">
                        <table class="table table-bordered table-condensed table-hover small kv-table">
                            <tr class="danger">
                                <td class="text-center">
                                    <div id="container<?php echo $num ?>" style="height: 250px; width: 430px"></div>
                                    <script src="/js/HighCharts/highcharts.js"></script>
                                    <script src="/js/HighCharts/modules/exporting.js"></script>
                                    <script type="text/javascript">
                                        Highcharts.chart('container<?php echo $num ?>', {
                                            data: {
                                                table: 'data_table'
                                            },
                                            chart: {
                                                type: 'column'
                                            },
                                            title: {
                                                text: ''
                                            },
                                            legend: {
                                                enabled: false
                                            },
                                            xAxis: {
                                                categories:
                                                <?php
                                                echo $categories;
                                                ?>
                                            },
                                            tooltip: {
                                                headerFormat: '<b>{point.x}</b><br/>',
                                                pointFormat: '{series.name}: {point.y}'
                                            },
                                            plotOptions: {
                                                column: {
                                                    stacking: 'normal',
                                                    dataLabels: {
                                                        enabled: true,
                                                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                                                    }
                                                }
                                            },
                                            yAxis: {
                                                min: 0,
                                                title: {
                                                    text: 'Последние данные'
                                                }
                                            },
                                            series: [{
                                                <?php
                                                echo $values;
                                                ?>
                                            }]
                                        });
                                    </script>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
