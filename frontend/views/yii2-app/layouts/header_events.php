<?php
/* @var $events */

?>

<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning"><?php echo count($events); ?></span>
    </a>
    <ul class="dropdown-menu" style="width: 400px">
        <li class="header"><?php echo count($events) ?>
            <?php echo Yii::t('app', 'событий в ближайшее время') ?></li>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <?php
                foreach ($events as $event) {
                    echo '<li style="line-height: 1.3; padding: 0 5px 0 0">
                                          <a href="/event/view?id=' . $event['_id'] . '">
                                          <small><i class="fa fa-clock-o"></i> ' . $event['createdAt'] . '</small>
                                          <p style="white-space: normal">
                                          <i class="fa fa-users text-aqua" style="margin: 3px"></i>' . $event['title'];
                    echo '</p></a></li>';
                }
                ?>
                <!-- end message -->
            </ul>
        </li>
        <li class="footer"><a href="/event"><?php echo Yii::t('app', 'Все события') ?></a></li>
    </ul>
</li>
