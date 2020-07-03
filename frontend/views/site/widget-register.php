<?php
/* @var $registers ServiceRegister[] */

use common\models\ServiceRegister;
use yii\helpers\Html;

?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'Журнал') ?></h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    <li><?php echo Html::a(Yii::t('app', 'Журнал'), "/service-register") ?></li>
                </ul>
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
                <tbody>
                <?php
                $count = 0;
                foreach ($registers as $register) {
                    $type = "тип не определен";
                    if ($register->type == ServiceRegister::TYPE_INFO)
                        $type = "<span class='badge' style='background-color: green; height: 12px; margin-top: -3px'> </span>&nbsp;Информация";
                    if ($register->type == ServiceRegister::TYPE_WARNING)
                        $type = "<span class='badge' style='background-color: orange; height: 12px; margin-top: -3px'> </span>&nbsp;Предупреждение";
                    if ($register->type == ServiceRegister::TYPE_ERROR)
                        $type = "<span class='badge' style='background-color: red; height: 12px; margin-top: -3px'> </span>&nbsp;Ошибка";

                    print '<tr><td>';
                    echo $register['createdAt'];
                    print '</td><td style="width: 120px">' . $type . '</td>
                           <td>' . $register["description"] . '</td>';
                    print '<td><span class="label label-info">' . $register->getEntityTitle() . '</span></td>';
                    print '</tr>';
                    $count++;
                    if ($count > 7) break;
                }
                ?>
                </tbody>
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
</div>
