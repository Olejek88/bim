<?php

use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = Yii::t('app', 'Календарь мероприятий');

?>

<script type="text/javascript">
    document.addEventListener("keydown", keyDownTextField, false);

    function keyDownTextField(e) {
        window.keyCode = e.keyCode;
    }
</script>

<div class="modal remote fade" id="modalAddOrder">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content loader-lg" id="modalContent">
        </div>
    </div>
</div>

<div class="site-index">
    <div class="body-content">
        <?php
        $JSCode = <<<JS
function(start, end) {
        $.get("/event/new",{ start: ""+start.format()+"" },
        function() {	})
        .done(function(data) {
            $('#modalAddEvent').modal('show');
            $('#modalContent').html(data);
    	})
    }
JS;
        $JSDropEvent = <<<JS
function( event, delta, revertFunc, jsEvent, ui, view ) {
	$.post("/event/move",{ event_start: ""+event.start.format()+"", event_id: ""+event.id+"" },	
	function(code) {
	    console.log('JSDropEvent');	    
	})
	.always(function(code) {
        var message = JSON.parse(code);
        if (message.code == 0) {
	        $('#calendar').fullCalendar('refetchEvents');
	        $('#calendar').fullCalendar('rerenderEvents');
	    }
        if (message.code == -1) {
            alert(message.message);
            revertFunc();
        }
	});  
}
JS;
        ?>

        <?= yii2fullcalendar\yii2fullcalendar::widget(array(
            'id' => 'calendar',
            'options' => [
                'lang' => Yii::t('app', 'ru'),
            ],
            'clientOptions' => [
                'selectable' => true,
                'selectHelper' => true,
                'droppable' => true,
                'editable' => true,
                'eventDrop' => new JsExpression($JSDropEvent),
                'defaultDate' => date('Y-m-d'),
                'defaultView' => 'month',
                'columnFormat' => 'ddd',
                'customButtons' => [
                    'delete' => [
                        'text' => ' ',
                        'click' => function () {
                            //you code
                        }
                    ]
                ],
                'header' => [
                    'left' => 'prev,next today month,agendaWeek,listYear',
                    'center' => 'title',
                    'right' => 'delete'
                ],
            ],
            'events' => Url::to(['/event/jsoncalendar'])
        ));
        ?>
    </div>
</div>
