<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;
use yii\web\View;

/** @var $this View */
/** @var array $tree */

$this->registerJsFile('/js/jquery.fancytree.contextMenu.js',
    ['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
$this->registerJsFile('/js/jquery.contextMenu.min.js',
    ['depends' => ['yii\jui\JuiAsset']]);
$this->registerCssFile('/css/ui.fancytree.css');
$this->registerCssFile('/css/jquery.contextMenu.min.css');

$bindObjectCallback = <<<JS
function (key, opt) {
    let node = $.ui.fancytree.getNode(opt.\$trigger);
    let extTree = $("#extMeasureChannelsTree").data('selectedObj', {
        path: node.data.path,
        id: node.data.nodeId,
        folder: node.folder
    });
    $.ajax({
        url: "/flows/link-obj-form",
        type: "get",
        success: function (data) {
            $("#modalLinkObjContent").html(data);
            $("#modalLinkObj").modal("show");
        }
   });
}
JS;
?>

<div id="extMeasureChannelsTree"></div>

<?php
echo FancytreeWidget::widget([
    'options' => [
        'id' => 'extMeasureChannelsTree',
        'source' => $tree,
        'keyboard' => false,
        'autoScroll' => true,
        'extensions' => ['contextMenu'],
        'contextMenu' => [
            'menu' => [
                'bindObject' => [
                    'name' => 'Связать с объектом',
                    'icon' => 'add',
                    'callback' => new JsExpression($bindObjectCallback),
                ],
            ],
        ],
//        'click' => new JsExpression(
//            'function(event, data) {
//                            console.log(data.node.data);
//                       }'
//        ),
    ],
]);
?>

<div class="modal remote fade" id="modalLinkObj">
    <div class="modal-dialog">
        <div class="modal-content loader-lg" id="modalLinkObjContent">
        </div>
    </div>
</div>

<?php
$this->registerJs('$("#modalLinkObj").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
?>

