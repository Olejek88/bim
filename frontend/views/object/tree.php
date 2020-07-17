<?php

use wbraganca\fancytree\FancytreeWidget;
use yii\helpers\Html;
use yii\web\JsExpression;

/* @var array $objects */
/* @var $sq string */

$this->title = Yii::t('app', 'Дерево объектов системы');
?>
    <table id="tree" style="width: 100%">
        <colgroup>
            <col width="*">
            <col width="*">
            <col width="220px">
            <col width="80px">
            <col width="*">
            <col width="60px">
            <col width="70px">
        </colgroup>
        <thead style="color: white" class="thead_tree">
        <tr>
            <th style="vertical-align: center" colspan="1">
                <table>
                    <tr>
                        <td>
                            <?=
                            Html::a('<button class="btn btn-success" type="button" id="addButton" style="padding: 1px 7px"
                            title="Добавить объект"><span class="fa fa-plus" aria-hidden="true"></span></button>',
                                ['new', 'root' => true],
                                ['data-toggle' => 'modal', 'data-target' => '#modalAdd']);
                            ?>
                            <button class="btn btn-info" type="button" id="expandButton" style="padding: 1px 5px"
                                    title="<?php echo Yii::t('app', 'Развернуть по уровням') ?>">
                                <span class="glyphicon glyphicon-expand" aria-hidden="true"></span>
                            </button>
                            <button class="btn btn-info" type="button" id="expandButton2" style="padding: 1px 5px"
                                    title="<?php echo Yii::t('app', 'Развернуть по уровням глубже') ?>">
                                <span class="glyphicon glyphicon-expand" aria-hidden="true"></span>
                            </button>
                            <button class="btn btn-info" type="button" id="collapseButton" style="padding: 1px 5px"
                                    title="
                        <?php echo Yii::t('app', 'Свернуть') ?>">
                                <span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span>
                            </button>
                        </td>
                        <td style="padding-left: 5px; padding-right: 5px">
                            <input name="search" placeholder="..." autocomplete="off" class="form-control"
                                   style="background-color: white"/>
                        </td>
                        <td style="padding-right: 5px">
                            <button id="btnResetSearch" style="padding: 1px 5px">&times;</button>
                        </td>
                        <td>
                            <span id="matches"></span>
                        </td>
                    </tr>
                </table>
            </th>
            <!--            <th style="vertical-align: center" colspan="3">
                <form action="" class="form-inline">
                    <table style="vertical-align: center">
                        <tr>
                            <td>
                                <?php
            /*                                echo Html::textInput('sq', $sq, [
                                                'class' => 'form-control',
                                                'style' => 'background-color: white',
                                                'id' => 'sq',
                                            ]);
                                            */ ?>
                            </td>
                            <td>
                                <?php
            /*                                if (!empty($sq)) {
                                                echo '<a class="btn btn-info" href="/objects/tree">x</a>';
                                            }
                                            */ ?>
                                <button class="btn btn-info"
                                        type="submit"><?php /*echo Yii::t('app', 'Искать') */ ?></button>
                            </td>
                        </tr>
                    </table>
                </form>
            </th>
-->
            <th align="center" colspan="6"><?php echo Yii::t('app', 'Объекты системы - каналы') ?></th>
        </tr>
        <tr>
            <th align="center"><?php echo Yii::t('app', 'Объект') ?></th>
            <th><?php echo Yii::t('app', 'Тип измерения') ?></th>
            <th><?php echo Yii::t('app', 'Значение') ?></th>
            <th><?php echo Yii::t('app', 'Тип') ?></th>
            <th><?php echo Yii::t('app', 'Оригинальное имя') ?></th>
            <th><?php echo Yii::t('app', 'Параметр') ?></th>
            <th><?php echo Yii::t('app', 'Действия') ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td class="alt"></td>
            <td class="center"></td>
            <td class="alt"></td>
            <td class="center"></td>
            <td class="alt"></td>
            <td class="center"></td>
        </tr>
        </tbody>
    </table>
    <div class="modal remote fade" id="modalRegister">
        <div class="modal-dialog" style="width: 1000px">
            <div class="modal-content loader-lg">
            </div>
        </div>
    </div>
    <div class="modal remote fade" id="modalParameter">
        <div class="modal-dialog" style="width: 1000px; height: 500px">
            <div class="modal-content loader-lg" id="modalParameterContent">
            </div>
        </div>
    </div>
    <div class="modal remote fade" id="modalAdd">
        <div class="modal-dialog" style="width: 800px; height: 400px">
            <div class="modal-content loader-lg" style="margin: 10px; padding: 10px" id="modalContent">
            </div>
        </div>
    </div>
    <div class="modal remote fade" id="modalAlarm">
        <div class="modal-dialog" style="width: 800px; height: 400px">
            <div class="modal-content loader-lg" style="margin: 10px; padding: 10px" id="modalContent">
            </div>
        </div>
    </div>
    <div class="modal remote fade" id="modalAddMeasure">
        <div class="modal-dialog" style="width: 800px; height: 400px">
            <div class="modal-content loader-lg" style="margin: 10px; padding: 10px" id="modalContent">
            </div>
        </div>
    </div>
    <div class="modal remote fade" id="modalChart">
        <div class="modal-dialog" style="width: 1200px; height: 600px">
            <div class="modal-content loader-lg" style="margin: 10px; padding: 10px" id="modalContent">
            </div>
        </div>
    </div>

<?php
$this->registerJsFile('/js/jquery.fancytree.contextMenu.js',
    ['depends' => ['wbraganca\fancytree\FancytreeAsset']]);
$this->registerJsFile('/js/jquery.contextMenu.min.js',
    ['depends' => ['yii\jui\JuiAsset']]);
$this->registerCssFile('/css/ui.fancytree.css');
$this->registerCssFile('/css/jquery.contextMenu.min.css');

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
            'extensions' => ['table', 'contextMenu', 'filter'],
            'contextMenu' => [
                'menu' => [
                    'new' => [
                        'name' => Yii::t('app', 'Добавить новый объект/канал'),
                        'icon' => 'add',
                        'callback' => new JsExpression('function(key, opt) {
                        var node = $.ui.fancytree.getNode(opt.$trigger);
                        if (node.folder==true) {
                            $.ajax({
                                url: "new",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid
                                },
                                success: function (data) { 
                                    $(\'#modalAdd\').modal(\'show\');
                                    $(\'#modalContent\').html(data);
                                }
                           }); 
                        }                        
                    }')
                    ],
                    'edit' => [
                        'name' => Yii::t('app', 'Редактировать'),
                        'icon' => 'edit',
                        'callback' => new JsExpression('function(key, opt) {
                        var node = $.ui.fancytree.getNode(opt.$trigger);
                        if (node.folder==true) {
                            $.ajax({
                                url: "edit",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    type: node.type,
                                    type_uuid: node.data.type_uuid
                                },
                                success: function (data) { 
                                    $(\'#modalAdd\').modal(\'show\');
                                    $(\'#modalContent\').html(data);
                                }
                           }); 
                        } else {
                            $.ajax({
                                url: "../object/edit",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid,
                                    type: node.type,                                },
                                success: function (data,status, request) {
                                    let isJson = true;
                                    try {
                                        JSON.parse(data);
                                    } catch(e) {
                                        isJson = false;
                                    }
                                    if (isJson) {
                                        var message = JSON.parse(data);
                                        alert(message.message);
                                    } else {
                                        $(\'#modalAdd\').modal(\'show\');
                                        $(\'#modalContent\').html(data);
                                    }
                                }
                            });  
                        }                       
                    }')
                    ],
                    'event' => [
                        'name' => Yii::t('app', 'Добавить событие'),
                        'icon' => 'add',
                        'callback' => new JsExpression('function(key, opt) {
                            var node = $.ui.fancytree.getNode(opt.$trigger);
                            $.ajax({
                                url: "../event/add",
                                type: "post",
                                data: {
                                    selected_node: node.key,
                                    folder: node.folder,
                                    uuid: node.data.uuid
                                },
                                success: function (data) { 
                                    $(\'#modalAddEvent\').modal(\'show\');
                                    $(\'#modalContentEvent\').html(data);
                                }
                            });
                    }')
                    ],
                    'delete' => [
                        'name' => Yii::t('app', 'Удалить'),
                        'icon' => "delete",
                        'callback' => new JsExpression('function(key, opt) {
                            var sel = $.ui.fancytree.getTree().getSelectedNodes();
                            $.each(sel, function (event, data) {
                                var node = $.ui.fancytree.getNode(opt.$trigger);
                                console.log(node.data.uuid);
                                $.ajax({
                                      url: "deleted",
                                      type: "post",
                                      data: {
                                          selected_node: data.key,
                                          folder: data.folder,
                                          type: node.type,
                                          uuid: node.data.uuid
                                      },
                                      success: function (code) {
                                        var message = JSON.parse(code);
				                        if (message.code === 0) {
                                            data.remove();
                                        } else {
                                            alert (message.message);
                                        }            
                                      }                                    
                                   });
                            });
                         }')
                    ]
                ]
            ],
            'table' => [
                'indentation' => 20,
                "titleColumnIdx" => "1",
                "type_titleColumnIdx" => "2",
                "valueColumnIdx" => "3",
                "measure_typeColumnIdx" => "4",
                "originalColumnIdx" => "5",
                "parameterColumnIdx" => "6",
                "linksColumnIdx" => "7"
            ],
            'renderColumns' => new JsExpression(
                'function(event, data) {
                    var node = data.node;
                    $tdList = $(node.tr).find(">td");
                    $tdList.eq(1).text(node.data.type_title);
                    $tdList.eq(2).html(node.data.value);           
                    $tdList.eq(3).html(node.data.measure_type);
                    $tdList.eq(4).html(node.data.original);
                    $tdList.eq(5).html(node.data.parameter);
                    $tdList.eq(6).html(node.data.links);
                }'
            )
        ]
    ]
);
?>
<?php
$this->registerJs('$("#modalAddMeasure").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalChart").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalRegister").on("hidden.bs.modal",
function () {
    $(this).removeData();
})');
$this->registerJs('$("#modalParameter").on("hidden.bs.modal",
function () {
    $(this).removeData();
    $("#modalAddAlarm").modal("hide");
    $("#modalAddEvent").modal("hide");
    $("#modalAddAttribute").modal("hide");
})');
$this->registerJs('$("#modalAddAlarm").on("hidden.bs.modal",
function () {
    $(this).removeData();
    $("#modalParameter").modal("hide");
})');
$this->registerJs('$("#modalAddEvent").on("hidden.bs.modal",
function () {
    $(this).removeData();
    $("#modalParameter").modal("hide");
})');
$this->registerJs('$("#modalAddAttribute").on("hidden.bs.modal",
function () {
    $(this).removeData();
    $("#modalParameter").modal("hide");
})');
$this->registerJs('$("#modalAdd").on("hidden.bs.modal",
function () {
    $(this).removeData();
    $("#modalParameter").modal("hide");
})');

$this->registerJs('$("#addButton").on("click",function() {
        var sel = $.ui.fancytree.getTree().getSelectedNodes();
        var count = $(sel).length;
        var i = 0;
        $.each(sel, function (event, data) {
            if (data.folder==false) {
                $.ajax({
                    url: "move",
                    type: "post",
                    data: {
                        selected_node: data.key,
                        user: $("#user_select").val()
                    },
                    success: function (data) {
                        i = i + 1;
                        if (i === count) {
                            window.location.replace("tree");
                        }                    
                    }
                });
            }
        });
    })');
$this->registerJs('$("#removeButton").on("click",function() {
        var sel = $.ui.fancytree.getTree().getSelectedNodes();
        var count = $(sel).length;
        var i = 0;
        $.each(sel, function (event, data) {
            if (data.folder==false) {
                $.ajax({
                    url: "remove",
                    type: "post",
                    data: {
                           selected_node: data.key,
                    },
                    success: function (data) {
                        i = i + 1;
                        if (i === count) {
                            window.location.replace("tree");
                        }                    
                    }
                });
            }
        });
    })');

$this->registerJs('$("#expandButton").on("click",function() {
    $("#tree").fancytree("getRootNode").visit(function(node){
        if(node.getLevel() < 2) {
            node.setExpanded(true);
        } else node.setExpanded(false);
    });
})');

$this->registerJs('$("#expandButton2").on("click",function() {
    $("#tree").fancytree("getRootNode").visit(function(node){
        if(node.getLevel() < 6) {
            node.setExpanded(true);
        } else node.setExpanded(false);
    });
})');

$this->registerJs('$("#collapseButton").on("click",function() {
    $("#tree").fancytree("getRootNode").visit(function(node){
        if(node.getLevel() < 2) {
            node.setExpanded(false);
        }
    });
})');
$this->registerJs('$("input[name=search]").on("keyup", function(e){
    var n = 0,
        tree = $.ui.fancytree.getTree(),
        args = "autoApply autoExpand fuzzy hideExpanders highlight leavesOnly nodata mode".split(" "),
        opts = {},
        filterFunc = $("#branchMode").is(":checked") ? tree.filterBranches : tree.filterNodes,
        match = $(this).val();

      $.each(args, function(i, o) {
          opts[o] = $("#" + o).is(":checked");
      });
      opts.mode = "hide";
      opts.autoExpand = true;

      if(e && e.which === $.ui.keyCode.ESCAPE || $.trim(match) === ""){
          $("button#btnResetSearch").click();
          return;
      }
      
      tree.filterNodes(function(node){
            let matchL = match.toLowerCase();
            if (node.title && node.title.toLowerCase().indexOf(matchL) !== -1){
                n = n + 1;
                return true;
            }
            if (node.data.inventory && node.data.inventory.toLowerCase().indexOf(matchL) !== -1){
                n = n + 1;
                return true;
            }
            if (node.data.serialText && node.data.serialText.toLowerCase().indexOf(matchL) !== -1){
                n = n + 1;
               return true;
            }
      },opts);

      $("button#btnResetSearch").attr("disabled", false);
      $("span#matches").text("(" + n + ")");
    }).focus();
');
$this->registerJs('$("button#btnResetSearch").click(function(e){
      $("input[name=search]").val("");
      $("span#matches").text("");
      $.ui.fancytree.getTree().clearFilter();
    }).attr("disabled", true);
');
$this->registerJs('
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === "node") {
                $.ui.fancytree.getTree().activateKey(decodeURIComponent(tmp[1]));
                var node = $.ui.fancytree.getTree("#tree").getActiveNode();
                node.setFocus();
                node.setExpanded(true).then(()=>{
                      node.span.scrollIntoView(true);
                      var viewportH = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
                      window.scrollBy(0, -viewportH/2);       
                });
            }            
        });
');
$this->registerJs('$("#modalAdd").on("show.bs.modal",
function () {
    var w0 = document.getElementById(\'w0\');
    if (w0) {
      w0.id = \'w1\';
    }
})');
?>