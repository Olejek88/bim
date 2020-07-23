<?php
/*  @var $values */
/*  @var $name string */
?>

<div class="measured-value-index" style="height: 700px">
    <div id="container" style="width:99%; height:60%; float:top"></div>
    <div id="container2" style="width:99%; height:40%; float:bottom">
        <table class="table table-bordered table-condensed table-hover small kv-table">
            <?php
            $cnt = 0;
            foreach ($values as $value) {
                echo '<tr><td>' . $value['date'] . '</td>
                      <td class="text-right">' . $value['value'] . '</td></tr>';
                if ($cnt++ > 8) break;
            }
            ?>
        </table>
    </div>
</div>

<script src="/js/HighCharts/highcharts.js"></script>
<script src="/js/HighCharts/modules/exporting.js"></script>

<script type="text/javascript">
    Highcharts.chart('container', {
        data: {
            table: 'datatable'
        },
        chart: {
            type: 'column'
        },
        title: {
            text: '<?php echo $name ?>'
        },
        xAxis: {
            categories: [
                <?php
                $first = 0;
                $bar = '';
                foreach ($values as $value) {
                    if ($first > 0)
                        $bar .= "," . PHP_EOL;
                    $bar .= '\'' . date('d/m H:i', strtotime($value->date)) . '\'';
                    $first++;
                }
                echo $bar;
                ?>
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: '<?php echo $name ?>'
            }
        },
        series: [{
            <?php
            $first = 0;
            $bar = "name: '" . $name . "',";
            $bar .= "data: [";
            foreach ($values as $value) {
                if ($first > 0)
                    $bar .= "," . PHP_EOL;
                $bar .= $value->value;
                $first++;
            }
            $bar .= "]";
            echo $bar;
            ?>
        }]
    });
</script>
