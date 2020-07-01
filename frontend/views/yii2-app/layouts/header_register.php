<?php
/* @var $register_unread */
?>

<li class="tasks-menu">
    <a href="/service-register" class="dropdown-toggle">
        <i class="fa fa-list"></i>
        <?php
        if ($register_unread > 0)
            echo '<span class="label label-danger">' . $register_unread . '</span>';
        ?>
    </a>
</li>
