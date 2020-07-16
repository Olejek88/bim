<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\web\JsExpression;
use yii\web\View;

/** @var $this View */
/** @var array $tree */

$this->registerJsFile('/js/custom/modules/list/jquery.fancytree.contextMenu.js',
    ['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
$this->registerJsFile('/js/custom/modules/list/jquery.contextMenu.min.js',
    ['depends' => ['yii\jui\JuiAsset']]);
$this->registerCssFile('/css/custom/modules/list/ui.fancytree.css');
$this->registerCssFile('/css/custom/modules/list/jquery.contextMenu.min.css');


$bindObjectCallback = <<<JS
function (key, opt) {
    var node = $.ui.fancytree.getNode(opt.\$trigger);
    $.ajax({
        url: "/flows/bind-object",
        type: "post",
        data: {
            path: node.data.path,
            id: node.data.nodeId,
            folder: node.folder,
        },
        success: function (data) {
            console.log('success', data);
        }
   });
}
JS;


echo FancytreeWidget::widget([
    'options' => [
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

