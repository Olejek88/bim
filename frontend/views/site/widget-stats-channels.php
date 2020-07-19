<?php
/* @var $channels */

?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'Статистика каналов') ?></h3>
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
                <th>Объект</th>
                <th>Тепло</th>
                <th>ЭЭ</th>
                <th>Вода</th>
                <th>Всего</th>
                </thead>
                <tbody>
                <?php
                $count = 0;
                foreach ($channels as $channel) {
                    print '<tr><td>';
                    echo $channel['object'];
                    print '</td><td style="text-align: center"><span class="label label-info">' . $channel['heat'] . '</span></td>';
                    print '<td style="text-align: center"><span class="label label-info">' . $channel['energy'] . '</span></td>';
                    print '<td style="text-align: center"><span class="label label-info">' . $channel['water'] . '</span></td>';
                    print '<td style="text-align: center"><span class="label label-info">' . $channel['all'] . '</span></td>';
                    print '</tr>';
                    $count++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
