<?php
/* @var $objects */

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;

?>

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'Дерево объектов/каналов') ?></h3>
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

    <div class="box-body">
        <table id="tree" style="width: 100%">
            <colgroup>
                <col width="*">
                <col width="*">
            </colgroup>
            <thead style="color: white" class="thead_tree">
            <tr>
                <th align="center" colspan="2"><?php echo Yii::t('app', 'Объекты системы - каналы') ?></th>
            </tr>
            <tr>
                <th align="center"><?php echo Yii::t('app', 'Объект') ?></th>
                <th><?php echo Yii::t('app', 'Значение') ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td class="alt"></td>
            </tr>
            </tbody>
        </table>
        <div class="modal remote fade" id="modalChart">
            <div class="modal-dialog" style="width: 1200px; height: 600px">
                <div class="modal-content loader-lg" style="margin: 10px; padding: 10px" id="modalContent">
                </div>
            </div>
        </div>

        <?php
        $this->registerCssFile('/css/ui.fancytree.css');
        echo FancytreeWidget::widget(
            [
                'options' => [
                    'id' => 'tree',
                    'source' => $objects,
                    'keyboard' => false,
                    'checkbox' => true,
                    'selectMode' => 2,
                    'quicksearch' => true,
                    'autoScroll' => true,
                    'extensions' => ['table'],
                    'table' => [
                        'indentation' => 20,
                        "titleColumnIdx" => "1",
                        "valueColumnIdx" => "2"
                    ],
                    'renderColumns' => new JsExpression(
                        'function(event, data) {
                    var node = data.node;
                    $tdList = $(node.tr).find(">td");
                    $tdList.eq(1).html(node.data.value);           
                }'
                    )
                ]
            ]
        );
        ?>
        <?php
        $this->registerJs('$("#modalChart").on("hidden.bs.modal",
            function () {
                $(this).removeData();
            })');
        ?>
    </div>
</div>
