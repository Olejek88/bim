<?php
/* @var $measures */

?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Последние данные</h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i></button>
            </div>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="table-responsive">
            <table class="table no-margin">
                <thead>
                <th>Дата</th>
                <th>Значение</th>
                </thead>
                <tbody>
                <?php
                $cnt = 0;
                foreach ($measures as $data) {
                    print '<tr><td>' . $data['date'] . '</td>';
                    print '<td style="text-align: center">' . $data['value'] . '</td>';
                    print '</tr>';
                    if ($cnt++ > 12) break;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
